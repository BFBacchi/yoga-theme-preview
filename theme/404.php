<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package yoga
 */
get_header(); ?>
<div class="yoga-breadcrumb-section">
    <div class="overlay">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <ul class="yoga-page-breadcrumb">
              <li><a href="<?php echo esc_url(home_url());?>"><i class="fa fa-home"></i></a></li>
              <li class="active"><a href="<?php echo esc_url(home_url());?>"><?php esc_attr_e('404','yoga'); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center yoga-section">
        <div class="yoga-error-404">
          <h1><?php esc_attr_e('4','yoga'); ?><i class="fa fa-exclamation-circle"></i><?php esc_attr_e('4','yoga'); ?></h1>
          <h4><?php esc_attr_e('Oops! That page can&rsquo;t be found.','yoga'); ?></h4>
          <p><?php esc_attr_e("It looks like nothing was found at this location. Maybe try one of the links below or a search?","yoga"); ?></p>
          <a href="<?php echo esc_url(home_url());?>" onClick="history.back();" class="btn btn-theme"><?php esc_attr_e('Go Back','yoga'); ?></a> </div>
      </div>
    </div>
  </div>
<?php
get_footer();