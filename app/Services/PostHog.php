<?php

namespace App\Services;

use PostHog\PostHog as PostHogSDK;

class PostHog
{
    public function capture(string $distinctId, string $event, array $properties = []): void
    {
        if (! config('posthog.api_key')) {
            return;
        }

        PostHogSDK::capture([
            'distinctId' => $distinctId,
            'event' => $event,
            'properties' => $properties,
        ]);
    }
}
