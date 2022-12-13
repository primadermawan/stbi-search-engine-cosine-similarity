<div class="container-fluid">

        <div class="span9 offset2 show-grid">
            <div class="row-fluid">
              <div class="span12">
                <form method="GET" action="<?php echo site_url('beranda_user/search_news'); ?>" class="form-search">
                    <input class="input-medium search-query" name="sea" type="text" placeholder="news title or author">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
              </div>
            </div>
            <div class="row-fluid">
              <?php if(!isset($result)): ?>
                <div class="span4"><h1>All News</h1></div>
              <?php else: ?>
                <div class="span4"><h1>result of search: '<?php echo $result; ?>'</h1></div>
              <?php endif; ?>
              <div class="span8"><?php echo (isset($pagination)) ? $pagination: ''; ?></div>                            
            </div>
            <?php foreach ($news->result() as $number): ?>
            <?php if($number->show == "y"): ?>
            <div class="well">
              <div><h2><?php echo $number->title; ?></h2></div>
               
              <div class="row-fluid">
                <?php if(!empty($number->media)): ?>
                <div class="span3">                  
                  <?php $meta = json_decode($number->media); if(($meta->file_ext == '.jpg') || ($meta->file_ext == '.jpeg') || ($meta->file_ext == '.JPG') || ($meta->file_ext =='.JPEG') || ($meta->file_ext == '.png') || ($meta->file_ext == '.PNG') || ($meta->file_ext == '.gif')): ?>
                    <img src="<?php echo base_url()."upload/".$meta->orig_name ?>">
                  <?php endif; ?>                  
                </div>
                <?php endif; ?>
                <div class="row-fluid">
                  <?php echo $number->content; ?>&nbsp;....<a href="<?php echo site_url('beranda_user'); ?>/lihat_berita/<?php echo $number->id; ?>">see more</a>
                </div>
              </div>               
              <br>
              <div class="row-fluid">
                <ul class="media-list">
                  <li class="media">
                  <a class="pull-left" href="<?php echo site_url('profile/user_profile'); echo "/"; echo $number->id_user; ?>">
                    <?php if(!empty($number->pict_name)): ?>
                      <img style="width: 64px; height: 64px;" class="media-object" src="<?php echo base_url(); ?>/pict_thumb/<?php echo $number->pict_name ?>" width="64" height="64">
                    <?php else: ?>
                      <img class="media-object" src="<?php echo base_url(); ?>/pict_thumb/ninja.png" width="64" height="64">
                    <?php endif; ?>
                  </a>
                  <div class="media-body">
                    <h5 class="media-heading"><?php echo $number->username ?></h5>
                    posted in <?php echo $number->time ?>
                  </div>
                </li>
                </ul>
              </div>

              <?php if(!empty($number->total)): ?>
                <div><span class="label label-info"><?php echo $number->total ?> comments</span></div>
              <?php else: ?>
                <div><span class="label"><?php echo "0" ?> comment</span></div>
              <?php endif ?>
              <?php if($this->session->userdata('group_id') == 10): ?>
              <?php if($number->show == 'y'): ?>
                <div class="row-fluid"><a href="<?php echo site_url('beranda_user/delete_post/user_news/'.$number->id); ?>">delete post</a></div>
              <?php else: ?>
                <div class="row-fluid"><a href="<?php echo site_url('beranda_user/undelete_post/user_news/'.$number->id); ?>">undelete post</a></div>
              <?php endif; ?>
              <?php endif; ?>
            </div>

            <?php elseif($number->show == "n"): ?>
            <div class="well">
              <div><h2>Removed Removed</h2></div>
              <hr></hr>
              <div>This content is removed by <?php echo $this->session->userdata('institution'); ?> administrator because its content is not proper&nbsp;....<a href="<?php echo site_url('beranda_user'); ?>/lihat_berita/<?php echo $number->id; ?>">see more</a></div>
              
              <hr></hr>
                <ul class="media-list">
                  <li class="media">
                  <a class="pull-left" href="<?php echo site_url('profile/user_profile'); echo "/"; echo $number->id_user; ?>">
                    <?php if(!empty($number->pict_name)): ?>
                      <img class="media-object" src="<?php echo base_url(); ?>/pict_thumb/<?php echo $number->pict_name ?>" width="64" height="64">
                    <?php else: ?>
                      <img class="media-object" src="<?php echo base_url(); ?>/pict_thumb/ninja.png" width="64" height="64">
                    <?php endif; ?>
                  </a>
                  <div class="media-body">
                    <h5 class="media-heading"><?php echo $number->username ?></h5>
                    posted in <?php echo $number->time ?>
                  </div>
                </li>
                </ul>

              <?php if(!empty($number->total)): ?>
                <div><span class="label label-info"><?php echo $number->total ?> comments</span></div>
              <?php else: ?>
                <div><span class="label"><?php echo "0" ?> comment</span></div>
              <?php endif ?>
              <?php if($this->session->userdata('group_id') == 10): ?>
              <?php if($number->show == 'y'): ?>
                <div class="row-fluid"><a href="<?php echo site_url('beranda_user/delete_post/user_news/'.$number->id); ?>">delete post</a></div>
              <?php else: ?>
                <div class="row-fluid"><a href="<?php echo site_url('beranda_user/undelete_post/user_news/'.$number->id); ?>">undelete post</a></div>
              <?php endif; ?>
              <?php endif; ?>
            </div>             
            <?php endif; ?>
            <?php endforeach ?>
            <?php echo (isset($pagination)) ? $pagination: ''; ?>
        </div>   
  </div>