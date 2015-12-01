<?php  ?>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <div class="cb-box cb-register">
            <div class="cb-row">
               <label for="username"><?php echo __( 'Username', $this->plugin_slug ); ?></label>
                <input type="text" name="username" value="<?php echo ( isset( $_POST['username'] ) ? $this->username : null ); ?>">
            </div>
             
            <div class="cb-row">
                <label for="email"><?php echo __( 'Email', $this->plugin_slug ); ?> </label>
                <input type="text" name="email" value="<?php echo ( isset( $_POST['email']) ? $this->email : null ); ?>">
            </div>
             
            <div class="cb-row">
                <label for="firstname"><?php echo __( 'First Name', $this->plugin_slug ); ?></label>
                <input type="text" name="first_name" value="<?php echo ( isset( $_POST['first_name']) ? $this->first_name : null ); ?>">
            </div>    

            <div class="cb-row">
                <label for="lastname"><?php echo __( 'Last Name', $this->plugin_slug ); ?></label>
                <input type="text" name="last_name" value="<?php echo ( isset( $_POST['last_name']) ? $this->last_name : null ); ?>">
            </div>
             
            <div class="cb-row">
                <label for="phone"><?php echo __( 'Phone', $this->plugin_slug ); ?></label>
                <input type="text" name="phone" value="<?php echo ( isset( $_POST['phone']) ? $this->phone : null ); ?>">
            </div>   

            <div class="cb-row">
                <label for="address"><?php echo __( 'Address', $this->plugin_slug ); ?></label>
                <input type="text" name="address" value="<?php echo ( isset( $_POST['address']) ? $this->address : null ); ?>">
            </div>    
            <div class="cb-row">
                <label for="terms_accepted"><?php echo __( 'I accept the terms', $this->plugin_slug ); ?></label>
                <input type="checkbox" name="terms_accepted" value="<?php echo ( isset( $_POST['terms_accepted']) ? $this->terms_accepted : null ); ?>">
            </div>
                <?php wp_nonce_field( 'create_user', 'user_nonce' ); ?>
                <div class="cb-row">
                <input type="submit" name="submit" value="<?php echo __('Register', $this->plugin_slug ); ?>"/>
            </div>
        </div>
    </form>