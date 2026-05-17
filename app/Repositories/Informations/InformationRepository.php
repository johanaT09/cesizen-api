<?php

namespace App\Repositories\Informations;

use App\Models\Information;

class InformationRepository
{
    public function getAllInformations($search = null, $categoryId = null)
    {
        $query = Information::with(['categorie', 'utilisateur'])
            ->where('est_actif', true);

        if ($categoryId) {
            $query->where('id_categorie', $categoryId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('titre_information', 'ILIKE', '%' . $search . '%')
                    ->orWhereHas('categorie', function ($q) use ($search) {
                        $q->where('libelle_categorie', 'ILIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('utilisateur', function ($q) use ($search) {
                        $q->where('prenom', 'ILIKE', '%' . $search . '%');
                    });
            });
        }

        return $query->orderBy('date_publication_information', 'desc')->paginate(9);
    }

    public function getInformationById($id)
    {
        return Information::with('categorie', 'utilisateur')->find($id);
    }
}
