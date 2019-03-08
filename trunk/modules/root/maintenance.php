<?php include_once("../../library/include/datlib.inc.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>GAzie è in fase di manutenzione</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style type="text/css">
            body { text-align: center; padding: 10%; font: 20px Helvetica, sans-serif; color: #333; background-image : url("../../library/images/sfondo.png");}
            h1 { font-size: 40px; margin: 0; }
            article { display: block; text-align: left; max-width: 650px; margin: 0 auto; }
            a { color: #dc8100; text-decoration: none; }
            a:hover { color: #333; text-decoration: none; }
            @media only screen and (max-width : 480px) {
                h1 { font-size: 40px; }
            }
        </style>
    </head>
    <body>
        <article>
            <h1>Al momento non è concesso accedere a GAzie.</h1>
            <p>la procedura di manutenzione non durerà ancora molto, riprova più tardi.</p>
            <p>Siamo spiacenti per il disagio causato, stiamo lavorando per voi.</p>
            <p id="signature">Per informazioni contatta <a href="mailto:<?php echo $maintenance; ?>"><?php echo $maintenance; ?></a> oppure riprova e <a href="../../index.php">Torna in GAzie</a></p>
        </article>
    </body>
</html>