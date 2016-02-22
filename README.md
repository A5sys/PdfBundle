PdfBundle
=============
#### Goal
This bundle allows generation of pdf docs by using the wrapper **phpwkhtmltopdf**, which uses himself the tool **wkhtmltopdf**.

The advantage of **wkhtmltopdf** is that it's Webkit processing html code, with JS and CSS functionnal, as if the code were ran in a browser.

####Why this bundle, KnpSnappyBundle already exist ?

KnpSnappyBundle does not work in all cases, particularly on Windows when generated HTML has external files (CSS, JS, images).
We hit some network access, because of the PHP proc_open function, which seems buggy.

After a successful try with phpwkhtmltopdf, this bundle was created to integrate with Symfony 2.

#### dependencies
Requires **phpwkhtmltopdf** and **wkhtmltopdf**.


## Installation

First install **wkhtmltopdf** (http://wkhtmltopdf.org/downloads.html)

Add dependencies in `composer.json` :
```json
"a5sys/pdf-bundle": "dev-master",
"mikehaertl/phpwkhtmltopdf": "~2.0"
```

Declare bundle in `AppKernel.php` :

```php
new A5sys\PdfBundle\A5sysPdfBundle(),
```

Note: the wrapper **phpwkhtmltopdf** is a library, not a bundle.

## Configuration

- Install wkhtmltopdf. Be sure not to have spaces in the path.
- Configure A5sys ProjectBundle

In **config.yml**, add :

```yaml
a5sys_pdf:
    binary: "C:/wkhtmltopdf/bin/wkhtmltopdf.exe"
    # temp_dir: "D:/tmp"
    # encoding: "UTF-8"
    # command_options:
    #    use_exec: true
    #    escape_args: false
    #    proc_options:
    #        bypass_shell: true
    #        suppress_errors: false
```

- binary: mandatory
- temp_dir: optional, default system temp dir (C:/windows/temp).
- encoding: optional, default UTF-8
- command_options: optional, by default the array is created with the default children values
-- use_exec: optional, default true, does the system use exec function instead of proc_open ?
-- escape_args: optional, default false, does the system escape parameters before integrating it to the command ?
-- proc_options: optional, default NULL, used when use_exec = false
---- bypass_shell: optional, default true, adviced when on Windows
---- suppress_errors: default false. Adviced to TRUE when on Windows. Generation can be done ,even if error occired. NO GUARRANTY pdf is correctly generated.

## Usage

Example given in a profession service.

\$this->templating is injected twig service.
\$this->pdfService is pdf service to be injected.

*utf8_decode* for header et footer. Necessary to get accents.

```php
use A5sys\PdfBundle\Service\PdfService;

class MyService
{
    /**
     * @var $data array
     */
    public function savePdf($data)
    {
        // HTML of pdf
        $html = $this->templating->render('MyAppBundle:Folder:my_pdf.pdf.twig', $data);

        // Header in HTML
        $htmlHeader = $this->templating->render('MyAppBundle:Folder:my_header.pdf.twig');

        // Footer in HTML
        $htmlFooter = $this->templating->render('MyAppBundle:Folder:my_footer.pdf.twig');

        // name and path to PDF file
        $fileName = uniqid() . ".pdf";
        $filePath = $this->constants['my_pdf_path'] . "/" . $fileName;

        // note : throws ServiceException
        // Generation and saving of the PDF

        $options = array(
           //'no-outline',
            'orientation' => 'Landscape',
            'header-html' => utf8_decode($htmlHeader),
            'header-spacing' => '2', // space between header and content in mm
            'footer-html' => utf8_decode($htmlFooter),
        );

        $this->pdfService->saveAs(
            $filePath,
            $html,
            $options
        );
    }
}
```

To the 2015-02-10, only saveAs is implemented for the wrapper phpwkhtmltopdf.

## Options

Variable **\$options ** allows to specify options to use for the generation.
At the begining, informations from configuration will b integrated to this array. If you want to override the value of the conf, simply add the key of the wrapper **phpwkhtmltopdf** to the array.

Specific **phpwkhtmltopdf** wrapper are here:
https://github.com/mikehaertl/phpwkhtmltopdf#special-global-options

Still in **\$options**, at the same level than specific wrapper options, you can add all options you want to give to **wkhtmltopdf**, as for example orientation , header-spacing, header-html or footer-html like shown in the example.

All wkhtmltopdf options here :
http://wkhtmltopdf.org/usage/wkhtmltopdf.txt

## Page break
In the example, \$html varaible contains all HTML to render in PDF. It is possible to tell explicitly where to page break, simply using CSS:

`<div style="page-break-after: always;"></div>`

## Page numbers

In the footer-html of the example, no param is given, but wkhtmltopdf do it for us ! We can get the current page number and even more, for example with this code :

```html
<!DOCTYPE html>
<html>
    <head>
        <script>
        function subst() {
          var vars={};
          var x=document.location.search.substring(1).split('&');
          for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
          var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
          for (var i in x) {
            var y = document.getElementsByClassName(x[i]);
            for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
          }
        }
        </script>
    </head>
    <body style="border:0; margin: 0;" onload="subst()">
        <table style="width: 100%">
          <tr>
            <td style="text-align: left">{{ 'now'|date('d/m/Y') }}</td>
            <td style="text-align: center">{{ 'pdf.archival.title'|trans|raw('html') }}</td>
            <td style="text-align: right">
              {{ 'page'|trans }} <span class="page"></span> {{ 'pageOn'|trans }} <span class="topage"></span>
            </td>
          </tr>
        </table>
    </body>
</html>
```

JS works ? Yes it's Webkit that process the page, even for header-html and footer-html

**Note** : Don't forget the doctype in footer and header, it won't work otherwise !

This HTML code displays the date to the left, a translated text in the center, and the text "page x sur y" to the right