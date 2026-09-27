<?php

namespace App\Http\Controllers;


use Spatie\LaravelPasses\Passes\AppleWalletPass;
use Spatie\LaravelMobilePass\Builders\Apple\EventTicketPassBuilder;
use Illuminate\Support\Str;
class Card extends Controller
{

    public function index()
    {
        $mobilePass = EventTicketPassBuilder::make()
            ->setOrganizationName('Fab Fo1ur Promotions')
            ->setSerialNumber((string) Str::uuid())            ->setDescription('The Beatles at Shea Stadium')
            ->addField('event', 'Beatles Live at Shea')
            ->addField('attendee', 'John Lennon', label: 'Name')
            ->addField('seat', 'Floor A, Row 12')

            ->setIconImage(
                storage_path('app/private/passgenerator/assets/icon.jfif')
            )
            ->save();

        return  $mobilePass;
    }
}
