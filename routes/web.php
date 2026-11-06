<?php

use App\Http\Controllers\AccountAuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Dev;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DesignRequestController;
use App\Http\Controllers\GraphicRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

/* ---------------- Boutique publique ---------------- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/le-complexe', [HomeController::class, 'about'])->name('about');

Route::get('/boutique', [ShopController::class, 'index'])->name('shop');
Route::get('/femme', [ShopController::class, 'index'])->defaults('gender', 'femme')->name('shop.femme');
Route::get('/homme', [ShopController::class, 'index'])->defaults('gender', 'homme')->name('shop.homme');
Route::get('/produit/{product}', [ShopController::class, 'show'])->name('product.show');

Route::get('/panier', [CartController::class, 'index'])->name('cart');
Route::post('/panier', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/commande', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/commande', [CheckoutController::class, 'store'])->middleware('throttle:forms')->name('checkout.store');
Route::get('/commande/{order}/merci', [CheckoutController::class, 'thanks'])->name('checkout.thanks');

Route::get('/sur-mesure', [DesignRequestController::class, 'create'])->name('design.create');
Route::post('/sur-mesure', [DesignRequestController::class, 'store'])->middleware('throttle:forms')->name('design.store');
Route::get('/sur-mesure/{designRequest}/merci', [DesignRequestController::class, 'thanks'])->name('design.thanks');

Route::get('/design-graphique', [GraphicRequestController::class, 'create'])->name('graphic.create');
Route::post('/design-graphique', [GraphicRequestController::class, 'store'])->middleware('throttle:forms')->name('graphic.store');
Route::get('/design-graphique/{graphicRequest}/merci', [GraphicRequestController::class, 'thanks'])->name('graphic.thanks');

Route::get('/suivi', [TrackingController::class, 'form'])->name('tracking');
Route::post('/suivi', [TrackingController::class, 'lookup'])->middleware('throttle:forms')->name('tracking.lookup');

/* ---------------- Comptes (connexion unique clients + administration) ---------------- */
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AccountAuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AccountAuthController::class, 'login'])->name('login.attempt');
    Route::get('/inscription', [AccountAuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AccountAuthController::class, 'register'])->middleware('throttle:forms')->name('register.store');
});
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AccountAuthController::class, 'logout'])->name('logout');
    Route::get('/mon-compte', [AccountController::class, 'index'])->name('account');
    Route::put('/mon-compte', [AccountController::class, 'update'])->name('account.update');
    Route::put('/mon-compte/mot-de-passe', [AccountController::class, 'password'])->name('account.password');
});

/* ---------------- Paiement Orange Money ---------------- */
Route::get('/paiement/{order}/orange-money', [PaymentController::class, 'pay'])->name('payment.om.pay');
Route::get('/paiement/{order}/retour', [PaymentController::class, 'return'])->name('payment.om.return');
Route::get('/paiement/{order}/annulation', [PaymentController::class, 'cancel'])->name('payment.om.cancel');
Route::post('/paiement/orange-money/notification', [PaymentController::class, 'notify'])->name('payment.om.notify');

/* ---------------- Espace admin (adresse non affichée sur le site) ---------------- */
Route::prefix(config('shop.admin_path', 'gestion'))->name('admin.')->group(function () {
    // L'ancienne adresse de connexion renvoie vers la connexion unique du site
    Route::get('/login', fn () => redirect()->route('login'))->name('login');

    // Pas de redirection vers la connexion : un visiteur non administrateur reçoit une page 404
    Route::middleware('admin')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('produits', Admin\ProductController::class)->except('show')
            ->parameters(['produits' => 'product'])->names('products');
        Route::delete('/images/{image}', [Admin\ProductController::class, 'destroyImage'])->name('images.destroy');
        Route::post('/images/{image}/principale', [Admin\ProductController::class, 'mainImage'])->name('images.main');

        Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/commandes', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/commandes/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('/commandes/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');
        Route::post('/commandes/{order}/verifier-paiement', [Admin\OrderController::class, 'verifyPayment'])->name('orders.verify');

        Route::get('/sur-mesure', [Admin\DesignRequestController::class, 'index'])->name('designs.index');
        Route::get('/sur-mesure/{designRequest}', [Admin\DesignRequestController::class, 'show'])->name('designs.show');
        Route::put('/sur-mesure/{designRequest}', [Admin\DesignRequestController::class, 'update'])->name('designs.update');

        Route::get('/infographie', [Admin\GraphicRequestController::class, 'index'])->name('graphics.index');
        Route::get('/infographie/{graphicRequest}', [Admin\GraphicRequestController::class, 'show'])->name('graphics.show');
        Route::put('/infographie/{graphicRequest}', [Admin\GraphicRequestController::class, 'update'])->name('graphics.update');

        Route::get('/apparence', [Admin\AppearanceController::class, 'edit'])->name('appearance.edit');
        Route::post('/apparence', [Admin\AppearanceController::class, 'update'])->name('appearance.update');
        Route::delete('/apparence/{slot}', [Admin\AppearanceController::class, 'destroy'])->name('appearance.destroy');

        Route::get('/parametres', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/parametres', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::put('/mot-de-passe', [Admin\SettingController::class, 'password'])->name('password.update');

        /* ----- Espace développeur ----- */
        Route::prefix('dev')->name('dev.')->middleware('developer')->group(function () {
            Route::get('/', [Dev\DevController::class, 'index'])->name('index');
            Route::get('/journaux', [Dev\DevController::class, 'logs'])->name('logs');
            Route::delete('/journaux', [Dev\DevController::class, 'clearLogs'])->name('logs.clear');
            Route::post('/action', [Dev\DevController::class, 'action'])->name('action');
            Route::get('/comptes', [Dev\DevController::class, 'users'])->name('users');
            Route::post('/comptes', [Dev\DevController::class, 'storeUser'])->name('users.store');
            Route::put('/comptes/{user}', [Dev\DevController::class, 'updateUser'])->name('users.update');
            Route::delete('/comptes/{user}', [Dev\DevController::class, 'destroyUser'])->name('users.destroy');
            Route::get('/base-de-donnees/{table?}', [Dev\DevController::class, 'database'])->name('database');
        });
    });
});
