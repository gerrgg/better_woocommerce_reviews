<?php

class BWCR_Create{
  public $products = array();

  public function __construct( $given_ids ){
    foreach( $given_ids as $id ){
      $name = get_the_title( $id );
      $img_src = get_the_post_thumbnail_url( $id );
      if( ! empty( $name ) && ! empty( $img_src ) ){
        array_push( $this->products, array( 'id' => $id, 'name' => $name, 'img_src' => $img_src ) );
      }
    }
  }

  public function get_form(){
    ?>
    <form id="bwcr_create_form" method="POST" action="<?php echo admin_url( 'admin-post.php' ) ?>" enctype="multipart/form-data" >
      <?php $this->get_username(); ?>
      <?php foreach( $this->products as $product ) : ?>
        <div id="<?php echo $product['id'] ?>" class="bwcr_product">
          <?php //$this->get_media_html( $product ); ?>
          <?php $this->get_details_html( $product ); ?>
          <?php $this->get_features_html( $product ); ?>
          <?php $this->get_headline_html( $product ); ?>
          <?php $this->get_review_html( $product ); ?>
        </div>
      <?php endforeach; ?>
      <input type="hidden" name="action" value="process_review">
      <button class="single_add_to_cart_button button alt" type="submit">Submit</button>
    </form>
    <?php
  }

  public function thank_you(){
    
  }

  public function get_username(){
    $user = wp_get_current_user();
    ?>
    <div id="<?php echo $product['id']; ?>-details" class="details">
      <div class="section x-padding">
        <div class="section y-spacing-top-med y-spacing-bottom-xl">
          <div class="d-flex align-items-center">
            <input type="text" name="username" class="form-control" value="<?php echo $user->display_name ?>" placeholder="Your name here" required />
          </div>
        </div>
      </div>
    </div>
    <?php
  }

  public function get_details_html( $product ){
    ?>
    <div id="<?php echo $product['id']; ?>-details" class="details">
      <div class="section x-padding">
        <div class="section y-spacing-top-med y-spacing-bottom-xl">
          <div class="d-flex align-items-center">
            <img class="tiny-img" src="<?php echo $product['img_src'] ?>" />
            <div>
              <p class="truncate"><?php echo $product['name'] ?></p>
              <?php $this->get_rating_html( $product ); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
    /*
    * This function used to dispaly start rating systems
    * $feature - is the word the user is evaluating, can be 'Durability', 'Fit', 'Comfort', etc.
    */
    public function get_rating_html( $product, $feature = 'product', $type = 'star' ){
      $func_to_call = 'get_' . $type . '_rating_html';
      $this->$func_to_call( $product, $feature );
    }

    public function get_star_rating_html( $product, $feature ){
      ?>
      <div data-product-id="<?php echo $product['id']?>" data-feature="<?php echo $feature; ?>">
        <?php if( $feature != 'product' ) echo "<h4 class='feature-header'>$feature</h4>"; ?>
        <span class="<?php echo $product['id'] . '-' . $feature;?> far fa-star fa-2x star-1" data-rating="1"></span>
        <span class="<?php echo $product['id'] . '-' . $feature;?> far fa-star fa-2x star-2" data-rating="2"></span>
        <span class="<?php echo $product['id'] . '-' . $feature;?> far fa-star fa-2x star-3" data-rating="3"></span>
        <span class="<?php echo $product['id'] . '-' . $feature;?> far fa-star fa-2x star-4" data-rating="4"></span>
        <span class="<?php echo $product['id'] . '-' . $feature;?> far fa-star fa-2x star-5" data-rating="5"></span>
        <input id="<?php echo $product['id'] . '-' . $feature ?>" type="hidden" name="<?php echo $product['id'] . '[' . $feature; ?>]" value="0" />
      </div>
      <?php
    }
    public function get_radio_rating_html( $product, $feature ){
      ?>

      <h4 class="feature-header"><?php echo $feature; ?></h4>
      <div class="d-flex radio-inline form-group">
        <input type="radio" class="radio-awnser" value="Too small" name="<?php echo $product['id'] . '[' . $feature . ']'; ?>" />
        <input type="radio" class="radio-awnser" value="Somewhat small" name="<?php echo $product['id'] . '[' . $feature . ']'; ?>" />
        <input type="radio" class="radio-awnser" value="Fit as expected" name="<?php echo $product['id'] . '[' . $feature . ']'; ?>" />
        <input type="radio" class="radio-awnser" value="Somewhat large" name="<?php echo $product['id'] . '[' . $feature . ']'; ?>" />
        <input type="radio" class="radio-awnser" value="Too large" name="<?php echo $product['id'] . '[' . $feature . ']'; ?>" />
      </div>
      <div class="radio-labels">
        <span style="text-align: left">Too Small</span>
        <span style="text-align: center">Fit as Expected</span>
        <span style="text-align: right">Too Large</span>
      </div>
      <?php
    }

    public function get_features_html( $product ){
      ?>
      <div id="<?php echo $product['id']; ?>-features">
        <div class="section x-padding">
          <div class="section y-spacing-top-med y-spacing-bottom-xl">
            <?php
              $this->get_radio_rating_html( $product, 'Fit' );
              $this->get_star_rating_html( $product, 'Durability' );
              $this->get_star_rating_html( $product, 'Comfort' );
              $this->get_star_rating_html( $product, 'Value' );
            ?>
          </div>
        </div>
      </div>
      <?php
    }

    public function get_media_html( $product ){
      ?>
      <div id="<?php echo $product['id']; ?>-media" class="media">
        <div class="section x-padding">
          <div class="section y-spacing-top-med y-spacing-bottom-xl">
          <h2>Add a photo</h3>
          <p>Shoppers find pictures alot more helpful than text alone.</p>
          <label for="file-upload" class="custom-file-upload">
              <i class="fas fa-camera"></i> Upload Photo
          </label>
          <input id="file-upload" type="file"/>
          <img id="#previewImage" src="https://placehold.it/100x100&text=No+PreView!">
        </div>
      </div>
    </div>
      <?php
    }

    public function get_headline_html( $product ){
      ?>
      <div id="<?php echo $product['id']; ?>-headline" class="headline">
        <div class="section x-padding">
          <div class="section y-spacing-top-med y-spacing-bottom-xl">
          <h3>Add a headline</h3>
          <label>Whats the most important thing to know?</label>
          <input class="notice-input form-control" type="text" name="<?php echo $product['id']?>[headline]">
        </div>
      </div>
    </div>
      <?php
    }

    public function get_review_html( $product ){
      ?>
      <div id="<?php echo $product['id']; ?>-review" class="review">
        <div class="section x-padding">
          <div class="section y-spacing-top-med y-spacing-bottom-xl">
          <h3>Write your review</h3>
          <label>What did you like or dislike? What did you use this product for?</label>
          <textarea class="notice-input" name="<?php echo $product['id']?>[review]" ></textarea>
        </div>
      </div>
    </div>
      <?php
    }


}
