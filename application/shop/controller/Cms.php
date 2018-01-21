<?php
/**
 * Cms.php
 *
 * =========================================================
 *
 * ----------------------------------------------
 * 官方网址: http://www.youshengxian.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app\shop\controller;

use data\service\Article as CmsService;
use think\Log;

/**
 * 内容控制器
 * 创建人：李吉
 * 创建时间：2017-02-06 10:59:23
 */
class Cms extends BaseController
{

    public function _empty($name)
    {}

    /**
     * 文章分类列表
     */
    public function articleList()
    {
        $article = new CmsService();
        $class_id = request()->get('id', '');
        $pid = request()->get('class_id', '');
        $condition = [
            'nca.class_id' => $class_id
        ];
        $result = $article->getArticleList(1, 0, $condition, 'nca.create_time desc');
        $cmsList = $article->getArticleClass(1, 0, '', 'sort');
        $articleClass = $article->getArticleClassDetail($class_id);
        $name = $articleClass['name'];
        $this->assign("name", $name);
        
        $this->assign('cmsList', $cmsList['data']);
        $this->assign('result', $result['data']);
        $this->assign("pid", $pid);
        $this->assign('class_id', $class_id);
        return view($this->style . 'Cms/articleList');
    }

    /**
     * 根据文章id获取文章详情
     */
    public function articleClassInfo($article_id='')
    {
        $cms = new CmsService();
        // 文章ID
        if(empty($article_id)){
            $article_id = request()->get('article_id', '');
        }
     
        $class_id = request()->get('id', '');
        $pid = request()->get('class_id', '');
        
        $info = null;
        if (! empty($article_id)) {
            $info = $cms->getArticleDetail($article_id);
            if(empty($info)){
                echo '<script>window.history.back(-1);</script>';
            }
            $class_id = $info["class_id"];
            $articleClass = $cms->getArticleClassDetail($class_id);
            $pid = $articleClass['pid'];
        }else{
            echo '<script>window.history.back(-1);</script>';
        }
        $content = htmlspecialchars_decode(html_entity_decode($info["content"]));
        $info["content"] = $content;
        $cmsList = $cms->getArticleClass(1, 0, '', 'sort');
        $this->assign('cmsList', $cmsList['data']);
        
        // 标题title(文章详情页面)
        $this->assign("title_before", $info['title']);
        $this->assign('info', $info);
        $this->assign("article_id", $article_id);
        $this->assign('class_id', $class_id);
        $this->assign('pid', $pid);
        
        //上一篇
        $prev_info = $cms->getArticleList(1,1,[
            "article_id" => array("<",$article_id),
            "nca.class_id" => $info["class_id"],
            "status" => 2
        ],"article_id desc");
        $this->assign("prev_info",$prev_info['data'][0]);
        //下一篇
        $next_info = $cms->getArticleList(1,1,[
            "article_id" => array(">",$article_id),
            "nca.class_id" => $info["class_id"],
            "status" => 2
        ],"article_id asc");
        $this->assign("next_info",$next_info['data'][0]);
        return view($this->style . 'Cms/articleClassInfo');
    }
}