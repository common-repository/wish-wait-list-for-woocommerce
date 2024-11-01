<?php
class brwwl_cookie_message {
    function __construct() {
        add_action('wp_footer', array($this, 'footer'));
    }
    function footer() {
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $options = $BeRocket_Wish_List->get_option();
        
        ?>
<div id="brwwl_cookie_notification">
    <p><?php echo br_get_value_from_array($options, array('text_settings', 'cookie_use_text')); ?></p>
    <button class="button brwwl_cookie_accept"><?php echo br_get_value_from_array($options, array('text_settings', 'cookie_use_button')); ?></button>
</div>
<script>
function brwwl_checkCookies(){
    let cookieDate = localStorage.getItem('brwwlCookieDate');
    let cookieNotification = document.getElementById('brwwl_cookie_notification');
    let cookieBtn = cookieNotification.querySelector('.brwwl_cookie_accept');

    if( !cookieDate || (+cookieDate + 31536000000) < Date.now() ){
        cookieNotification.classList.add('show');
    }

    cookieBtn.addEventListener('click', function(){
        localStorage.setItem( 'brwwlCookieDate', Date.now() );
        cookieNotification.classList.remove('show');
    })
}
brwwl_checkCookies();
</script>
<style>
#brwwl_cookie_notification{
  display: none;
  justify-content: space-between;
  align-items: flex-end;
  position: fixed;
  bottom: 15px;
  left: 50%;
  width: 900px;
  max-width: 90%;
  transform: translateX(-50%);
  padding: 25px;
  background-color: white;
  border-radius: 4px;
  box-shadow: 2px 3px 10px rgba(0, 0, 0, 0.4);
  z-index: 90000;
}

#brwwl_cookie_notification p{
  margin: 0;
  font-size: 0.7rem;
  text-align: left;
  color: $color_text;
}


@media (min-width: 576px){
  #brwwl_cookie_notification.show{
    display: flex;
  }
  .brwwl_cookie_accept{
    margin: 0 0 0 25px;
  }
}

@media (max-width: 575px){
  #brwwl_cookie_notification.show{
    display: block;
    text-align: left;
  }
  .brwwl_cookie_accept{
    margin: 10px 0 0 0;
  }
}
</style>
        <?php
    }
}
new brwwl_cookie_message();