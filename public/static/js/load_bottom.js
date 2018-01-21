/**
 *  功能描述：加载底部的版权信息
 */
$(function(){
	// $.ajax({
	// 	url : __URL(APPMAIN + "/task/copyrightisload"),
	// 	type : "post",
	// 	dataType : "json",
	// 	success : function(data) {
	// 		$is_load=data["is_load"];
	// 		$bottom_info=data["bottom_info"];
	// 		var copyright_meta = "";
	// 		if($is_load>0){
	// 			$("#copyright_logo").attr("src", STATIC + data["default_logo"]);
	// 			$("#copyright_companyname").attr("href", "http://www.youshengxian.com");
	// 			$("#copyright_companyname").html("山东亿拓信息技术有限公司&nbsp;");
	// 			$("#copyright_desc").html("Copyright © 2015-2025 亿成优鲜");
	// 		}else{
	// 			$("#copyright_logo").attr("src", __IMG($bottom_info["copyright_logo"]));
	// 			$("#copyright_logo_wap").attr("src", __IMG($bottom_info["copyright_logo"]));
	// 			$("#copyright_companyname").attr("href", $bottom_info["copyright_link"]);
	// 			$("#copyright_companyname").html($bottom_info["copyright_companyname"]);
	// 			$("#copyright_desc").html($bottom_info["copyright_desc"]);
	// 			$("#login_copyright").hide();
	// 			$("#rigister_copyright").hide();
	// 		}
	// 		//备案信息
	// 		if($bottom_info["copyright_meta"] != "" && $bottom_info["copyright_meta"] != null){
	// 			copyright_meta = "备案号："+$bottom_info["copyright_meta"];
	// 		}
	// 		$("#copyright_meta").html(copyright_meta);
	// 	}
	// });
});