<?php

namespace App\Providers;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\DocumentType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Enums\PropertyUserRole;
use App\Enums\SelectPersonType;
use App\Enums\StepType;
use App\Enums\UserRole;
use App\Models\AdminUser;
use App\Models\Answer;
use App\Models\ClientUser;
use App\Models\Condition;
use App\Models\Conveyancer;
use App\Models\ConveyancerUser;
use App\Models\Form;
use App\Models\Property;
use App\Models\ProvidedAnswer;
use App\Models\Section;
use App\Models\Step;
use App\Models\User;
use App\Models\ValidationRule;
use App\Services\CompaniesHouseService\CompaniesHouseService;
use App\Services\OnboardingLettersService\OnboardingLettersService;
use App\Services\PdfService\PdfService;
use App\Services\QrService\QrService;
use App\Services\YotiIdvService\YotiIdvService;
use App\Services\YotiSignService\YotiSignService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Mustache_Engine;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(CompaniesHouseService::class, fn () => new CompaniesHouseService);

        $this->app->singleton(OnboardingLettersService::class, fn () => new OnboardingLettersService);

        $this->app->singleton(StripeClient::class, fn () => new StripeClient([
            'api_key' => config('services.stripe.secret_key'),
            'client_id' => config('services.stripe.client_id'),
        ]));

        $this->app->singleton(Mustache_Engine::class, fn () => new Mustache_Engine);

        $this->app->singleton(YotiSignService::class, fn () => new YotiSignService(
            key: config('services.yoti_sign.key'),
            sandbox: config('services.yoti_sign.sandbox'),
        ));

        $this->app->singleton(PdfService::class, fn () => new PdfService);

        $this->app->singleton(YotiIdvService::class, fn () => new YotiIdvService(
            sdkId: config('services.yoti_idv.sdk_id'),
            pem: config('services.yoti_idv.pem'),
            sandbox: config('services.yoti_idv.sandbox'),
        ));

        $this->app->bind(QrService::class, fn () => new QrService);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'condition' => Condition::class,
            'answer' => Answer::class,
            'step' => Step::class,
            'section' => Section::class,
            'form' => Form::class,
            'conveyancer' => Conveyancer::class,
            'property' => Property::class,
            'provided_answer' => ProvidedAnswer::class,
            'validation_rule' => ValidationRule::class,
            // All user types should use the same map
            'user' => User::class,
            'admin_user' => AdminUser::class,
            'conveyancer_user' => ConveyancerUser::class,
            'client_user' => ClientUser::class,
        ]);

        $typeRegistry = app(TypeRegistry::class);
        $typeRegistry->register(new LaravelEnumType(StepType::class));
        $typeRegistry->register(new LaravelEnumType(AnswerType::class));
        $typeRegistry->register(new LaravelEnumType(FormGroup::class));
        $typeRegistry->register(new LaravelEnumType(UserRole::class));
        $typeRegistry->register(new LaravelEnumType(DocumentType::class));
        $typeRegistry->register(new LaravelEnumType(SelectPersonType::class));
        $typeRegistry->register(new LaravelEnumType(ConditionType::class));
        $typeRegistry->register(new LaravelEnumType(PropertyUserRole::class));
        $typeRegistry->register(new LaravelEnumType(PropertyType::class));
        $typeRegistry->register(new LaravelEnumType(FormType::class));
    }
}
