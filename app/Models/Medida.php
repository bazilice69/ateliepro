<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medida extends Model
{
    use HasFactory, BelongsToLoja;

    protected $guarded = [];

    protected $casts = [
        'data_medicao' => 'date',
    ];

    /**
     * Rótulos (label => coluna) das 33 medidas da ficha, na ordem do papel da
     * estilista. Centralizado aqui para as telas reutilizarem e para comparar
     * medições entre provas.
     */
    public const CAMPOS_FICHA = [
        'Alto Busto'          => 'alto_busto',
        'Busto'               => 'busto_torax',
        'Tórax'               => 'torax_medida',
        'Cintura'             => 'cintura',
        'Altura Corpo'        => 'altura_corpo',
        'Centro Frente'       => 'centro_frente',
        'Centro Costas'       => 'centro_costas',
        'Altura do Busto'     => 'altura_busto',
        'Distância de Busto'  => 'distancia_busto',
        'Cava a Cava'         => 'cava_a_cava',
        'Ombro a Ombro'       => 'ombro_a_ombro',
        'Ombro'               => 'ombro',
        'Lateral'             => 'lateral',
        'Diagonais'           => 'diagonais',
        'Circ. Cava'          => 'circ_cava',
        'Quadril'             => 'quadril',
        'Altura do Quadril'   => 'altura_quadril',
        'Quadril Alto'        => 'quadril_alto',
        'Quadril Baixo'       => 'quadril_baixo',
        'Altura Q. Alto'      => 'altura_q_alto',
        'Altura Q. Baixo'     => 'altura_q_baixo',
        'Comp. Saia'          => 'comp_saia',
        'Comp. Cauda'         => 'comp_cauda',
        'Decotes'             => 'decotes',
        'Braço'               => 'braco',
        'Comp. Manga'         => 'manga',
        'Cotovelo Dobrado'    => 'cotovelo_dobrado',
        'Altura do Cotovelo'  => 'altura_cotovelo',
        'Punho'               => 'punho',
        'Mão'                 => 'mao',
        'Cabeça da Manga'     => 'cabeca_manga',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }

    /**
     * Retorna só os campos da ficha que estão preenchidos (rótulo => valor).
     */
    public function preenchidas(): array
    {
        $out = [];
        foreach (self::CAMPOS_FICHA as $rotulo => $coluna) {
            if (filled($this->{$coluna})) {
                $out[$rotulo] = $this->{$coluna};
            }
        }

        return $out;
    }

    /**
     * Compara esta medição com uma anterior e devolve, por campo da ficha, o
     * valor atual, o anterior e a variação numérica (quando dá pra calcular).
     *
     * Formato de cada item:
     *   ['rotulo', 'coluna', 'atual', 'anterior', 'delta' (float|null), 'mudou' (bool)]
     */
    public function compararCom(?Medida $anterior): array
    {
        $linhas = [];

        foreach (self::CAMPOS_FICHA as $rotulo => $coluna) {
            $atual = $this->{$coluna};
            $ant   = $anterior?->{$coluna};

            // Ignora campos vazios nas duas medições.
            if (blank($atual) && blank($ant)) {
                continue;
            }

            $delta = null;
            if (is_numeric($this->soNumero($atual)) && is_numeric($this->soNumero($ant))) {
                $delta = round((float) $this->soNumero($atual) - (float) $this->soNumero($ant), 2);
            }

            $linhas[] = [
                'rotulo'   => $rotulo,
                'coluna'   => $coluna,
                'atual'    => $atual,
                'anterior' => $ant,
                'delta'    => $delta,
                'mudou'    => (string) $atual !== (string) $ant,
            ];
        }

        return $linhas;
    }

    /**
     * Extrai a parte numérica de uma medida ("68 cm" => "68") para comparar.
     */
    private function soNumero($valor): ?string
    {
        if (blank($valor)) {
            return null;
        }

        $limpo = str_replace(',', '.', preg_replace('/[^0-9,.\-]/', '', (string) $valor));

        return $limpo === '' ? null : $limpo;
    }
}
