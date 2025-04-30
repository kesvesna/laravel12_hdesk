<?php


use App\Http\Controllers\AuthController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

//Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//$request->fulfill();
//return response()->json(['message' => 'Email verified successfully']);
//})->middleware(['signed'])->name('verification.verify');

//Route::post('/email/verification-notification', function (Request $request) {
//$request->user()->sendEmailVerificationNotification();
//return response()->json(['message' => 'Verification link sent!']);
//})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::post('/forgot-password', function (Request $request) {
$request->validate(['email' => 'required|email']);

$status = Password::sendResetLink(
$request->only('email')
);

return $status === Password::RESET_LINK_SENT
? response()->json(['message' => __($status)])
: response()->json(['message' => __($status)], 400);
});

Route::post('/reset-password', function (Request $request) {
$request->validate([
'token' => 'required',
'email' => 'required|email',
'password' => 'required|confirmed|min:8',
]);

$status = Password::reset(
$request->only('email', 'password', 'password_confirmation', 'token'),
function ($user, $password) {
$user->forceFill([
'password' => Hash::make($password)
])->save();

event(new PasswordReset($user));
}
);

return $status === Password::PASSWORD_RESET
? response()->json(['message' => __($status)])
: response()->json(['message' => __($status)], 400);
});
});
