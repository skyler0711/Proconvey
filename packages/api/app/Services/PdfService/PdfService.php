<?php

namespace App\Services\PdfService;

use Dompdf\Dompdf;
use Dompdf\Options;

final class PdfService
{
    /**
     * Default CSS to apply to the PDF.
     * https://github.com/sindresorhus/modern-normalize
     */
    protected string $css = <<<'CSS'
*,::after,::before{box-sizing:border-box}html{-moz-tab-size:4;tab-size:4}html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji'}hr{height:0;color:inherit}abbr[title]{text-decoration:underline dotted}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace,SFMono-Regular,Consolas,'Liberation Mono',Menlo,monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit}button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;line-height:1.15;margin:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button}::-moz-focus-inner{border-style:none;padding:0}:-moz-focusring{outline:1px dotted ButtonText}:-moz-ui-invalid{box-shadow:none}legend{padding:0}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}

@font-face {
    font-family: "GT Eesti Pro";
    font-weight: 400;
    font-style: normal;
    src: url({{ resource_path('fonts/GTEestiProDisplay-Regular.ttf') }}) format('truetype');
}

@font-face {
    font-family: "GT Eesti Pro";
    font-weight: 500;
    font-style: normal;
    src: url({{ resource_path('fonts/GTEestiProDisplay-Medium.ttf') }}) format('truetype');
}

@font-face {
    font-family: "GT Eesti Pro";
    font-weight: 700;
    font-style: normal;
    src: url({{ resource_path('fonts/GTEestiProDisplay-Bold.ttf') }}) format('truetype');
}

body {
    font-family: "GT Eesti Pro";
}

.text-primary {
    color: #674186;
}

.bg-mint {
    background-color: #62C0C1;
}

.border-mint {
    border-color: #62C0C1;
}

.border-primary-ring {
    border-color: #E9E4EE;
}
CSS;

    /**
     * Render the HTML to a PDF.
     *
     * @param  string  $html The HTML to render.
     */
    public function render(string $html): PdfResult
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);

        $domPdf = new Dompdf($options);
        $domPdf->setPaper('A4', 'portrait');

        $html = "<style>$this->css</style>$html";

        $domPdf->loadHtml($html);

        $domPdf->render();

        return new PdfResult(
            content: $domPdf->output(),
            numberOfPages: $domPdf->getCanvas()->get_page_count(),
        );
    }
}
