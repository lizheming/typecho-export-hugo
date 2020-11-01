<?php

class Export2Hugo_Action extends Typecho_Widget implements Widget_Interface_Do
{
  /**
   * 导出文章
   *
   * @access public
   * @return void
   */
  public function doExport() {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $sql=<<<TEXT
  select u.screenName author,url authorUrl,title,type,text,created,c.status status,password,t2.category,t1.tags,slug from {$prefix}contents c
  left join
  (select cid,CONCAT('"',group_concat(m.name SEPARATOR '","'),'"') tags from {$prefix}metas m,{$prefix}relationships r where m.mid=r.mid and m.type='tag' group by cid ) t1
  on c.cid=t1.cid
  left join
  (select cid,CONCAT('"',GROUP_CONCAT(m.name SEPARATOR '","'),'"') category from {$prefix}metas m,{$prefix}relationships r where m.mid=r.mid and m.type='category' group by cid) t2
  on c.cid=t2.cid
  left join ( select uid, screenName ,url from {$prefix}users)  as u
  on c.authorId = u.uid
  where c.type in ('post', 'page')
TEXT;
    $contents = $db->fetchAll($db->query($sql));
    
    $dir = sys_get_temp_dir()."/Export2Hugo";
    if(file_exists($dir)) {
      exec("rm -rf $dir");
    }
    mkdir($dir);

    $contentDir = $dir."/content/";
    mkdir($contentDir);
    mkdir($contentDir."/posts");

    foreach($contents as $content) {
      $title = $content["title"];
      $categories = $content["category"];
      $tags = $content["tags"];
      $slug = $content["slug"];
      $time = date('Y-m-d H:i:s', $content["created"]);
      $text = str_replace("<!--markdown-->", "", $content["text"]);
      $draft = $content["status"] !== "publish" || $content["password"] ? "true" : "false";
      $hugo = <<<TMP
---
title: "$title"
categories: [ $categories ]
tags: [ $tags ]
draft: $draft
slug: "$slug"
date: "$time"
---

$text
TMP;

      $filename = str_replace(array(" ","?","\\","/" ,":" ,"|", "*" ),'-',$title).".md";
      
      if($content["type"] === "post") {
        $filename = "posts/".$filename;
      }
      file_put_contents($contentDir.$filename, $hugo);
      echo $contentDir.$filename;
    }
  
    $filename = "hugo.".date('Y-m-d').".zip";
    $outputFile = $dir."/".$filename;
    exec("cd $dir && zip -q -r $outputFile content");
    
    header("Content-Type:application/zip");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-length: " . filesize($outputFile));
    header("Pragma: no-cache"); 
    header("Expires: 0"); 

    readfile($outputFile);
  }

  /**
   * 绑定动作
   *
   * @access public
   * @return void
   */
  public function action() {
    $this->widget('Widget_User')->pass('administrator');
    $this->on($this->request->is('export'))->doExport();
  }
}