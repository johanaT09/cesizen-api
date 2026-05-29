<?php

namespace App\Http\Controllers\ActiviteDetente;

use App\Http\Controllers\Controller;
use App\Services\ActiviteDetente\ActiviteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    protected $activiteService;

    public function __construct(ActiviteService $activiteService)
    {
        $this->activiteService = $activiteService;
    }

    public function getAllActivites(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $catId = $request->query('category_id');
        $typeId = $request->query('type_id');

        $activites = $this->activiteService->getAllActivites($search, $catId, $typeId);

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }

    public function getActiviteById($id): JsonResponse
    {
        $activite = $this->activiteService->getActiviteById($id);

        if (!$activite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Activité non trouvée'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activite
        ]);
    }

    public function getActivitesByType($typeId): JsonResponse
    {
        $activites = $this->activiteService->getActivitesByType($typeId);

        if ($activites->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Aucune activité trouvée pour ce type',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }

    public function getActivitesByCategorie($categorieId): JsonResponse
    {
        $activites = $this->activiteService->getActivitesByCategorie($categorieId);

        if ($activites->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Aucune activité trouvée pour cette catégorie',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }



    public function toggleFavori(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id_utilisateur;

        $result = $this->activiteService->toggleFavori($userId, $id);

        if (!$result) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $attached = count($result['attached']) > 0;

        return response()->json([
            'message' => $attached ? 'Activité ajoutée aux favoris' : 'Activité retirée des favoris',
            'is_favori' => $attached
        ], 200);
    }

    public function getFavoris(Request $request): JsonResponse
    {
        $userId = $request->user()->id_utilisateur;
        $favoris = $this->activiteService->getFavorisByUtilisateur($userId);

        return response()->json([
            'status' => 'success',
            'data' => $favoris
        ]);
    }

    public function disableActivite($id): JsonResponse
    {
        $activite = $this->activiteService->desactiverActivite($id);

        if (!$activite) {
            return response()->json(['message' => 'Activité non trouvée'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'L\'activité a été désactivée',
            'data' => $activite
        ]);
    }

    public function getAdminActivites(Request $request): JsonResponse
    {
        $search  = $request->query('search');
        $catId   = $request->query('category_id');
        $typeId  = $request->query('type_id');
        $perPage = $request->query('per_page', 20); // 20 éléments par défaut pour l'admin

        $activites = $this->activiteService->getAdminActivites($search, $catId, $typeId, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => $activites
        ]);
    }

    public function addActivite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titre_activite'       => 'required|string|max:255',
            'description_activite' => 'nullable|string',
            'lien_ressource'       => 'nullable|string',
            'duree_estimee'        => 'required|string',
            'id_type'              => 'required|exists:type,id_type',
            'id_categorie'         => 'required|exists:categorie_activite,id_categorie',
            'image'                => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // 👈 "required" au lieu de "nullable"
            'video'                => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:102400',
        ], [
            'image.required' => 'L\'image de couverture est obligatoire.',
            'image.image'    => 'Le fichier doit être une image valide.',
        ]);

        $validated['contenu_activite'] = $request->input('description_activite');
        unset($validated['description_activite']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('activites', 'public');
            $validated['image_path'] = $path;
        }

        if ($request->hasFile('video')) {
            $pathVideo = $request->file('video')->store('activites/videos', 'public');
            $validated['lien_ressource'] = $pathVideo; // On écrase le texte par le chemin du fichier
        } elseif ($request->has('lien_ressource')) {
            $validated['lien_ressource'] = $request->input('lien_ressource');
        }

        $activite = $this->activiteService->createActivite($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Activité ajoutée avec succès',
            'data' => $activite
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titre_activite'       => 'sometimes|required|string|max:255',
                'description_activite' => 'sometimes|nullable|string',
                'lien_ressource'       => 'sometimes|nullable|string',
                'duree_estimee'        => 'sometimes|required|string',
                'est_actif'            => 'sometimes|required|boolean',
                'id_type'              => 'sometimes|required|exists:type,id_type',
                'id_categorie'         => 'sometimes|required|exists:categorie_activite,id_categorie',
                'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            if ($request->has('description_activite')) {
                $validated['contenu_activite'] = $request->input('description_activite');
                unset($validated['description_activite']);
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('activites', 'public');
                $validated['image_path'] = $path;
            }

            $activite = $this->activiteService->updateActivite($id, $validated);

            if (!$activite) {
                return response()->json(['message' => 'Activité non trouvée'], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Activité mise à jour avec succès',
                'data'    => $activite
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function getSession(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id_utilisateur;
        $progression = $this->activiteService->getProgression($userId, $id);

        return response()->json([
            'status' => 'success',
            'progression' => $progression
        ]);
    }

    public function saveSession(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id_utilisateur;
        $progression = $request->input('progression', 0);
        $estTermine = $request->input('est_termine', false);

        $this->activiteService->saveProgression($userId, $id, $progression, $estTermine);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function getStartedSessions(Request $request): JsonResponse
    {
        $userId = $request->user()->id_utilisateur;
        $activities = $this->activiteService->getStartedActivities($userId);

        return response()->json([
            'status' => 'success',
            'data' => $activities
        ]);
    }

    public function getVideoStream($id)
    {
        $activite = $this->activiteService->getActiviteById($id);

        if (!$activite || !$activite->lien_ressource) {
            abort(404, "Activité ou vidéo introuvable.");
        }

        $path = storage_path('app/public/' . $activite->lien_ressource);

        if (!file_exists($path)) {
            abort(404, "Le fichier vidéo n'existe pas sur le serveur.");
        }

        return response()->file($path, [
            'Content-Type' => 'video/mp4',
        ]);
    }
}
