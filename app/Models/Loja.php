hasMany(User::class);
    }

    // Uma loja tem um histórico de assinaturas
    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }
}