<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:buyer,seller'],
        ]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'verification_code' => $code,
                'email_verified_at' => null,
            ]);

            $this->sendVerificationEmail($user, $code);
        } catch (Throwable $e) {
            if (isset($user)) {
                $user->delete();
            }

            return response()->json([
                'status' => 'email_failed',
                'message' => 'Verifizierungsmail konnte nicht gesendet werden. Bitte später erneut versuchen.',
            ], 503);
        }

        return response()->json([
            'status' => 'verification_sent',
            'message' => 'Bitte verifiziere deinen Account mit dem Code aus der E-Mail.',
            'email' => $user->email,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'Benutzer nicht gefunden.'], 422);
        }

        if ($user->verification_code !== $request->code) {
            return response()->json(['message' => 'Der Verifizierungscode ist ungültig.'], 422);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'verification_code' => null,
        ])->save();

        return response()->json([
            'status' => 'verified',
            'message' => 'Dein Konto wurde verifiziert.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Ungültige E-Mail oder falsches Passwort.'], 401);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'not_verified',
                'message' => 'Bitte verifiziere zuerst deinen Account.',
            ], 403);
        }

        $token = Str::random(40);
        $user->forceFill(['api_token' => $token])->save();

        return response()->json([
            'status' => 'logged_in',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(60);
            try {
                $user->forceFill(['password_reset_token' => $token])->save();
                $this->sendPasswordResetEmail($user, $token);
            } catch (Throwable $e) {
                return response()->json([
                    'status' => 'email_failed',
                    'message' => 'Reset-E-Mail konnte nicht gesendet werden. Bitte später erneut versuchen.',
                ], 503);
            }
        }

        return response()->json([
            'status' => 'reset_sent',
            'message' => 'Falls die E-Mail existiert, wurde ein Link gesendet.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('password_reset_token', $request->token)->first();

        if (! $user) {
            return response()->json(['message' => 'Ungültiger oder abgelaufener Link.'], 422);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
        ])->save();

        return response()->json([
            'status' => 'password_reset',
            'message' => 'Dein Passwort wurde erfolgreich geändert.',
        ]);
    }

    private function sendVerificationEmail(User $user, string $code): void
    {
        $user->notify(new \App\Notifications\VerifyEmailNotification($code));
    }

    private function sendPasswordResetEmail(User $user, string $token): void
    {
        $user->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
