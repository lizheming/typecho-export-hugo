<?php
include_once 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
  <div class="body container">
    <div class="typecho-page-title">
      <h2><?php _e('数据导出'); ?></h2>
    </div>
    <div class="row typecho-page-main" role="form">
      <div id="dbmanager-plugin" class="col-mb-12 col-tb-8 col-tb-offset-2">
        <p>在您点击下面的按钮后，Typecho 会创建一个 Zip 压缩文件，包含所有的文章和页面，供您保存到计算机中。</p>
        <p>使用过程中如果有问题，请到 <a href="https://github.com/lizheming/typecho-export-hugo/issues">Github</a> 提出。</p>
        <form action="<?php $options->index('/action/export2hugo?export'); ?>" method="post">
          <ul class="typecho-option typecho-option-submit" id="typecho-option-item-submit-3">
            <li>
              <button type="submit" class="primary"><?php _e('开始导出！'); ?></button>
            </li>
          </ul>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
include 'footer.php';
?>
