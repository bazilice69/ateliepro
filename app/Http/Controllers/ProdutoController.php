<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Acervo;
use App\Models\AcervoFoto;
use App\Support\Imagens;

class ProdutoController extends Controller
{
    // Lista as peças do acervo
    public function index()
    {
        $pecas = Acervo::orderBy('codigo')->get();
        return view('acervo.index', compact('pecas'));
    }

    // Abre a tela de cadastro
    public function create()
    {
        // Gera um código automático simples (ex: VN-4592), mas a pessoa pode apagar e digitar outro
        $codigoSugerido = 'VN-' . rand(1000, 9999);
        return view('acervo.create', compact('codigoSugerido'));
    }

    // Salva no banco de dados
    public function store(Request $request)
    {
        $lojaId = Auth::user()->loja_id;
        $this->normalizarDinheiro($request, ['valor_locacao', 'valor_venda', 'caucao']);

        $request->validate([
            // Código único DENTRO da loja (cada loja tem sua própria numeração).
            'codigo' => ['required', 'string', Rule::unique('acervos', 'codigo')->where('loja_id', $lojaId)],
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string',
            'valor_locacao' => 'required|numeric',
            'fotos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $peca = Acervo::create($request->except(['loja_id', 'fotos']));

        // Fotos enviadas no cadastro (a primeira vira capa).
        $this->salvarFotos($request, $peca);

        return redirect()->route('acervo.show', $peca)->with('success', 'Peça cadastrada no acervo com sucesso!');
    }

    // Tela de edição da peça (editar dados + gerenciar a galeria de fotos).
    public function edit(Acervo $peca)
    {
        $peca->load('fotos');
        return view('acervo.edit', compact('peca'));
    }

    // Atualiza os dados da peça.
    public function update(Request $request, Acervo $peca)
    {
        $lojaId = Auth::user()->loja_id;
        $this->normalizarDinheiro($request, ['valor_locacao', 'valor_venda', 'caucao']);

        $dados = $request->validate([
            'codigo' => ['required', 'string', Rule::unique('acervos', 'codigo')->where('loja_id', $lojaId)->ignore($peca->id)],
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string',
            'tamanho' => 'nullable|string|max:50',
            'cor' => 'nullable|string|max:50',
            'valor_locacao' => 'required|numeric',
            'status' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'fotos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $peca->update(collect($dados)->except('fotos')->toArray());

        // Novas fotos adicionadas na edição.
        $this->salvarFotos($request, $peca);

        return redirect()->route('acervo.edit', $peca)->with('success', 'Peça atualizada!');
    }

    // Define uma foto como capa.
    public function definirCapa(Acervo $peca, AcervoFoto $foto)
    {
        abort_unless($foto->acervo_id === $peca->id, 404);

        $peca->fotos()->update(['capa' => false]);
        $foto->update(['capa' => true]);
        // Mantém a coluna `foto` (capa) sincronizada para listagem/compatibilidade.
        $peca->update(['foto' => $foto->caminho]);

        return back()->with('success', 'Foto de capa definida!');
    }

    // Remove uma foto da galeria.
    public function removerFoto(Acervo $peca, AcervoFoto $foto)
    {
        abort_unless($foto->acervo_id === $peca->id, 404);

        Storage::disk('public')->delete($foto->caminho);
        $eraCapa = $foto->capa;
        $foto->delete();

        // Se a capa foi removida, promove a próxima foto a capa.
        if ($eraCapa) {
            $nova = $peca->fotos()->first();
            if ($nova) {
                $nova->update(['capa' => true]);
                $peca->update(['foto' => $nova->caminho]);
            } else {
                $peca->update(['foto' => null]);
            }
        }

        return back()->with('success', 'Foto removida.');
    }

    /** Remove a peça inteira (e suas fotos). */
    public function destroy(Acervo $peca)
    {
        foreach ($peca->fotos as $f) {
            Storage::disk('public')->delete($f->caminho);
        }
        $peca->delete(); // cascade apaga as fotos no banco

        return redirect()->route('acervo.index')->with('success', 'Peça removida do acervo.');
    }

    /**
     * Salva as fotos enviadas (upload múltiplo) na galeria da peça.
     * A primeira foto da peça vira capa automaticamente.
     */
    private function salvarFotos(Request $request, Acervo $peca): void
    {
        if (!$request->hasFile('fotos')) {
            return;
        }

        $lojaId = Auth::user()->loja_id;
        $jaTemCapa = $peca->fotos()->where('capa', true)->exists();
        $ordem = (int) $peca->fotos()->max('ordem');

        foreach ($request->file('fotos') as $arquivo) {
            if (!$arquivo || !$arquivo->isValid()) {
                continue;
            }
            // Comprime/redimensiona (evita erro com fotos grandes e economiza disco).
            $caminho = Imagens::salvarComprimida($arquivo, "acervo/{$lojaId}");
            $ehCapa = !$jaTemCapa;
            $peca->fotos()->create([
                'loja_id' => $lojaId,
                'caminho' => $caminho,
                'capa' => $ehCapa,
                'ordem' => ++$ordem,
            ]);
            if ($ehCapa) {
                $peca->update(['foto' => $caminho]); // sincroniza capa na coluna legada
                $jaTemCapa = true;
            }
        }
    }

    /**
     * Cadastro RÁPIDO de peça (via modal, dentro da tela de Locação).
     * Pede o essencial e devolve JSON para o front adicionar no seletor sem
     * recarregar a página.
     */
    public function storeRapido(Request $request)
    {
        $lojaId = Auth::user()->loja_id;
        $this->normalizarDinheiro($request, ['valor_locacao', 'caucao']);

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('acervos', 'codigo')->where('loja_id', $lojaId)],
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'valor_locacao' => 'required|numeric|min:0',
            'caucao' => 'nullable|numeric|min:0',
        ]);

        $dados['status'] = Acervo::STATUS_DISPONIVEL;
        $peca = Acervo::create($dados);

        return response()->json([
            'id' => $peca->id,
            'label' => "[{$peca->codigo}] {$peca->nome} (R$ " . number_format((float) $peca->valor_locacao, 2, ',', '.') . ')',
        ]);
    }

    /**
     * Catálogo estilo revista (gerado com o acervo ATUAL — reflete as fotos/
     * peças do momento). Filtros opcionais por categoria e status.
     * Página pronta para "Salvar como PDF" e enviar à cliente.
     */
    public function catalogo(Request $request)
    {
        $query = Acervo::query()->with('fotos');

        // Filtro por categoria (opcional).
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Por padrão mostra só as DISPONÍVEIS (o que a cliente pode alugar);
        // ?todas=1 inclui todas.
        if (!$request->boolean('todas')) {
            $query->where('status', Acervo::STATUS_DISPONIVEL);
        }

        $pecas = $query->orderBy('categoria')->orderBy('nome')->get();
        $loja = Auth::user()->loja ?? null;

        return view('acervo.catalogo', [
            'pecas' => $pecas,
            'loja' => $loja,
            'categoriaFiltro' => $request->categoria,
        ]);
    }

    // Etiqueta imprimível da peça (com QR Code que abre a ficha).
    public function etiqueta(Acervo $peca)
    {
        // URL que o QR Code vai abrir ao ser escaneado: a ficha da peça.
        $urlFicha = route('acervo.show', $peca);
        $loja = Auth::user()->loja ?? null;

        return view('acervo.etiqueta', compact('peca', 'urlFicha', 'loja'));
    }

    // Histórico / "vida" da peça: locações, provas e serviços.
    public function show(Acervo $peca)
    {
        $peca->load([
            'locacoes.cliente',
            'provas.cliente',
            'servicos',
        ]);

        return view('acervo.show', compact('peca'));
    }
}