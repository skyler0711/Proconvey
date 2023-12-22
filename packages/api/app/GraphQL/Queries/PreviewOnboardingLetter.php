<?php

namespace App\GraphQL\Queries;

use App\Services\OnboardingLettersService\OnboardingLettersService;

final class PreviewOnboardingLetter
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var OnboardingLettersService */
        $service = app(OnboardingLettersService::class);

        return [
            'html' => $service->getHtml(
                content: $args['content'] ?? '',
                header: $args['header'] ?? '',
                footer: $args['footer'] ?? '',
                preview: true,
            ),
        ];
    }
}
