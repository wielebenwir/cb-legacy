<?php  ?>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <div>
    <label for="username">Username <strong>*</strong></label>
    <input type="text" name="username" value="<?php echo ( isset( $_POST['username'] ) ? $this->username : null ); ?>">
    </div>
     
    <div>
    <label for="password">Password <strong>*</strong></label>
    <input type="password" name="password" value="<?php echo ( isset( $_POST['password'] ) ? $this->password : null ); ?>">
    </div>
     
    <div>
    <label for="email">Email <strong>*</strong></label>
    <input type="text" name="email" value="<?php echo ( isset( $_POST['email']) ? $this->email : null ); ?>">
    </div>
     
    <div>
    <label for="firstname">First Name</label>
    <input type="text" name="fname" value="<?php echo ( isset( $_POST['fname']) ? $this->first_name : null ); ?>">
    </div>    

    <div>
    <label for="lastname">Last Name</label>
    <input type="text" name="lname" value="<?php echo ( isset( $_POST['lname']) ? $this->last_name : null ); ?>">
    </div>
     
    <div>
    <label for="phone">Phone</label>
    <input type="text" name="phone" value="<?php echo ( isset( $_POST['phone']) ? $this->phone : null ); ?>">
    </div>   

    <div>
    <label for="address">Adress</label>
    <input type="text" name="address" value="<?php echo ( isset( $_POST['address']) ? $this->address : null ); ?>">
    </div>    
    <div>
    <label for="terms_accepted">terms_accepted</label>
    <input type="checkbox" name="terms_accepted" value="<?php echo ( isset( $_POST['terms_accepted']) ? $this->terms_accepted : null ); ?>">
    </div>
        <input type="submit" name="submit" value="Register"/>

    </form>