<div class="wrap">

    <!-- <h3>Loggers</h3> -->
    <form method="post">

        <?php

        do_action( "simple_history/developer_loggers/before_plugins_table" );

        $available_loggers = $this->get_available_loggers();

        if ( $available_loggers ) {

            printf('

                <h2>%4$s</h2>

                <table class="widefat wp-list-table plugins">
                    <thead>
                        <tr>
                            <th>%3$s</th>
                            <th>%1$s</th>
                            <th>%2$s</th>
                        </tr>
                    </thead>
                    <tbody>
                ',
                __("Name", "simple-history"),
                __("Description", "simple-history"),
                __("Enabled", "simple-history"),
                __("Enabled loggers and plugins", "simple-history")
            );

            foreach ( $available_loggers as $logger ) {

                $is_enabled = $this->is_logger_enabled( $logger["slug"] );

                printf('
                    <tr class="%5$s">
                        <td>
                            <label>
                                <input type="checkbox" %4$s name="enabled_loggers[]" value="%3$s">
                            </label>
                        </td>
                        <td>%1$s</td>
                        <td>%2$s</td>
                    </tr>
                    ',
                    $logger["info"]["name"], // 1
                    $logger["info"]["description"], // 2
                    $logger["slug"] , // 3
                    checked(true, $is_enabled, false), // 4
                    $is_enabled ? "active" : "inactive"
                );

            } // for each logger

            printf('
                </tbody>
                </table>
            ');

        } // if available loggers

        do_action( "simple_history/developer_loggers/after_plugins_table" );

        ?>

        <input type="hidden" name="<?php echo $this->slug ?>_action" value="save_settings">

        <?php

        wp_nonce_field( 'save_settings', "{$this->slug}_nonce" );

        submit_button( null );

        ?>

    </form>

</div>
