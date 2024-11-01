<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $subject; ?></title>
    </head>
    <body>
        <div style='background-color: #f6f6f6;font-size: 14px;font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;box-sizing: border-box;line-height: 1.6;width: 100% !important;height: 100%;'>
            <table class="body-wrap" style="background-color: #f6f6f6;width: 100%;">
                <tbody>
                    <tr>
                        <td></td>
                        <td class="container" width="600" style="vertical-align: top;display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;">
                            <div class="content" style="max-width: 600px;margin: 0 auto;display: block;padding: 20px;">
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" style="background: #fff;border: 1px solid #e9e9e9;border-radius: 3px;">
                                    <tbody>
                                        <tr>
                                            <td class="content-wrap" style="vertical-align: top;padding: 20px;">
                                                <?php echo $message; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="footer" style="width: 100%;clear: both;color: #999;padding: 20px;">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="aligncenter content-block" style="vertical-align: top;padding: 0 0 20px;text-align: center;">
                                                    <a style="color: #999;font-size: 12px;text-decoration: underline;" href="<?php echo site_url(); ?>"><?php echo get_bloginfo('name'); ?></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
