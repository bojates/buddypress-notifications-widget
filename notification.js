jQuery(document).ready(function () {

    var jq = jQuery;
    jq(document).on('click', '.bp-notifications-widget-clear-link', function () {
        var $this = jq(this);
        var nonce = get_var_in_url($this.attr('href'), '_wpnonce');
        $this.text('clearing...');
        jq.post(ajaxurl, {
            'action': 'bpdev_notification_clear_notifications',
            '_wpnonce': nonce,
            cookie: encodeURIComponent(document.cookie)


        }, function (resp) {
            if (resp == '1') {

                // remove notification count in the adminbar
                jq('#bp-adminbar-notifications-menu').find('span').remove();
                jq('#bp-adminbar-notifications-menu>ul').remove();

                //  BuddyOPress notification widget title.
                jq('.notification-count-in-title').text('(0)');
                //remove all notifications
                //let us remove the widget completely
                if ( $this.parents('.widget_buddydev_bpnotification_widget').get(0))
                    $this.parents('.widget_buddydev_bpnotification_widget').remove();

                //we may not need below lines, these are just safe measure in case the widget does not have our class as there are some real great theme authors out there
                $this.remove();//in case someone has used it somewhere else
                jq('.bp-notification-widget-notifications-list').remove();

            }
        });
        return false;//prevent default action
    });


    function get_var_in_url(url, name) {

        var urla = url.split("?");
        var qvars = urla[1].split("&");//so we hav an arry of name=val,name=val
        for (var i = 0; i < qvars.length; i++) {
            var qv = qvars[i].split("=");
            if (qv[0] == name)
                return qv[1];
        }
        return '';
    }
});