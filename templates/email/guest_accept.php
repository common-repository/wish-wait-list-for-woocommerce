<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $subject; ?></title>
    </head>
    <body>
        <div style='background-color: #f6f6f6;font-size: 14px;font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;box-sizing: border-box;line-height: 1.6;width: 100% !important;height: 100%;'>
            <p>Products was added to waitlist on site <a href="<?php echo get_site_url(); ?>"><?php echo get_site_url(); ?></a>. We will send information about product in stock if needed.</p>
            <p><a style="display:block; padding: 8px; background-color: #e31; color: white;" href="<?php echo get_site_url(); ?>?brwwl_hash=<?php echo $md5hash; ?>">Yes, send instock product</a></p>
        </div>
    </body>
</html>
