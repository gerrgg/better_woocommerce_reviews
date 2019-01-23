<?php

class BWCR_Create{
  public $products;

  public function __construct( $given_ids ){
    foreach( $given_ids as $id ){
      $name = get_the_title( $id );
      $img_src = get_the_post_thumbnail_url( $id );
      if( ! empty( $name ) && ! empty( $img_src ) ){
        $this->products[$id] = array( 'name' => $name, 'img_src' => $img_src );
      }
    }
  }

  public function get_form(){
    ?>
    <form method="POST" action="<?php echo admin_url( 'admin-post.php' ) ?>" >
      <?php foreach( $this->products as $id => $product ) : ?>
        <?php $this->get_details_html( $product ); ?>
        <?php $this->get_rating_html( $product ); ?>
        <?php $this->get_features_html( $product ); ?>
        <?php $this->get_media_html( $product ); ?>
        <?php $this->get_headline_html( $product ); ?>
        <?php $this->get_review_html( $product ); ?>
      <?php endforeach; ?>
    </form>
    <?php
  }

  public function get_details_html( $product ){
    ?>
    <div id="<?php echo $id; ?>-details">
      <div class="product-details d-flex align-items-center">
        <img class="tiny-img" src="<?php echo $product['img_src'] ?>" />
        <p><?php echo $product['name'] ?></p>
      </div>
    </div>
    <?php
  }

    public function get_rating_html( $product ){
      ?>
      <div id="<?php echo $id; ?>-rating" class="form-group">
        <h2>Overall Rating</h2>
        <span class="far fa-star fa-3x"></span>
        <span class="far fa-star fa-3x"></span>
        <span class="far fa-star fa-3x"></span>
        <span class="far fa-star fa-3x"></span>
        <span class="far fa-star fa-3x"></span>
      </div>
      <?php
    }

    public function get_features_html( $product ){
      ?>
      <div id="<?php echo $id; ?>-features"></div>
      <?php
    }

    public function get_media_html( $product ){
      ?>
      <div id="<?php echo $id; ?>-media"></div>
      <?php
    }

    public function get_headline_html( $product ){
      ?>
      <div id="<?php echo $id; ?>-headline"></div>
      <?php
    }

    public function get_review_html( $product ){
      ?>
      <div id="<?php echo $id; ?>-review"></div>
      <?php
    }


}
