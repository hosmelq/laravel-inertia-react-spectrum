<?php

declare(strict_types=1);

namespace App\Listeners;

use function Sentry\captureException;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Throwable;

class SetMediaDimensions
{
    /**
     * Sets the dimensions (height and width) of the added media as custom
     * properties if the media is an image (JPEG or PNG).
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        try {
            $media = $event->media;

            if (! in_array(mb_strtolower($media->mime_type), ['image/jpeg', 'image/jpg', 'image/png'], true)) {
                return;
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($media->stream());

            $media->setCustomProperty('height', $image->height());
            $media->setCustomProperty('width', $image->width());

            $media->save();
        } catch (Throwable $e) {
            captureException($e);
        }
    }
}
