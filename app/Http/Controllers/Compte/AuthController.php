<?php

namespace App\Http\Controllers\Compte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Compte\AuthService;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'mot_de_passe' => 'required|string',
            ]);

            $result = $this->authService->login($validated['email'], $validated['mot_de_passe']);
            $user = $result['user'];

            return response()->json([
                'message' => 'Connexion réussie',
                'token' => $result['token'],
                'user' => [
                    'id_utilisateur' => $user->id_utilisateur,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'id_role' => (int) $user->id_role,
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Données de connexion manquantes ou mal formatées.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getmessage() ?: 'Identifiants incorrects.'
            ], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:utilisateur,email'
        ], [
            'email.exists' => "Cette adresse e-mail ne correspond à aucun compte."
        ]);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));

        return response()->json([
            'status' => 'success',
            'message' => 'L\'e-mail de réinitialisation a été envoyé avec succès.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:utilisateur,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required'  => 'Le champ mot de passe est obligatoire.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le jeton de réinitialisation ou l\'e-mail est invalide.'
            ], 422);
        }

        if (now()->subMinutes(60)->gt($record->created_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le jeton de réinitialisation a expiré.'
            ], 422);
        }

        DB::table('utilisateur')
            ->where('email', $request->email)
            ->update(['mot_de_passe' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Votre mot de passe a bien été réinitialisé.'
        ]);
    }
}
