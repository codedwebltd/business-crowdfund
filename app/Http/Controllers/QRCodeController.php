<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;

class QRCodeController extends Controller
{
    public function generate(Request $request)
    {
        $data = $request->input('data', '');

        if (empty($data)) {
            return response('Invalid QR data', 400);
        }

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($data);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}
