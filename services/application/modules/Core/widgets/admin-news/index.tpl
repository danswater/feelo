<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <john@socialengine.com>
 */
?>
<?php /*
<div class="admin_home_news">
  <h3 class="sep">
    <span><?php echo $this->translate("News & Updates") ?></span>
  </h3>

  <?php if( !empty($this->channel) ): ?>
    <ul>
      <?php foreach( $this->channel['items'] as $item ): ?>
        <li>
          <div class="admin_home_news_date">
            <?php echo $this->locale()->toDate(strtotime($item['pubDate']), array('size' => 'long')) ?>
          </div>
          <div class="admin_home_news_info">
            <a href="<?php echo @$item['link'] ? $item['link'] : $item['guid'] ?>" target="_blank">
              <?php echo $item['title'] ?>
            </a>
            <span class="admin_home_news_blurb">
              <?php echo $this->string()->truncate($this->string()->stripTags($item['description']), 350) ?>
            </span>
          </div>
        </li>
      <?php endforeach; ?>
      <li>
        <div class="admin_home_news_date">
          &nbsp;
        </div>
        <div class="admin_home_news_info">
          &#187; <a href="http://www.socialengine.com/blog"><?php echo $this->translate("More SE News") ?></a>
        </div>
      </li>
    </ul>

  <?php elseif( $this->badPhpVersion ): ?>

  <div>
    <?php echo $this->translate('The news feed requires the PHP DOM extension.') ?>
  </div>

  <?php else: ?>

  <div>
    <?php echo $this->translate('There are no news items, or we were unable to fetch the news.') ?>
  </div>

  <?php endif; ?>
</div>

<?php if( false ): ?>
  <br />
  <span class="rss_fetched_timestamp">
    <?php if( $this->isCached ): ?>
      <?php echo $this->translate('Results last fetched at %1$s',
          $this->locale()->toDateTime($this->channel['fetched'])) ?>
    <?php else: ?>
      <?php echo $this->translate('Results are current') ?>
    <?php endif ?>
  </span>
<?php endif ?>
*/ ?>

