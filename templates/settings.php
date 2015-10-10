<div class="wrap">

    <!-- <h3>Loggers</h3> -->
    <form method="post">

        <ul>
            <?php

            $available_loggers = $this->get_available_loggers();

            foreach ( $available_loggers as $logger ) {

                printf('
                    <li>
                        <h4>%1$s</h4>
                        <p>%2$s</p>
                        <p>
                            <label>
                                <input type="checkbox" %4$s name="enabled_loggers[]" value="%3$s">
                                Enable
                            </label>
                        </p>
                    </li>
                    ',
                    $logger["info"]["name"], // 1
                    $logger["info"]["description"], // 2
                    $logger["slug"] , // 3
                    checked(true, $this->is_logger_enabled( $logger["slug"] ), false) // 4
                );

            }

            ?>
        </ul>

        <input type="hidden" name="<?php echo $this->slug ?>_action" value="save_settings">
        <?php

        wp_nonce_field( 'save_settings', "{$this->slug}_nonce" );

        submit_button( null );

        ?>

    </form>

</div>
