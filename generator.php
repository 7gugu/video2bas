<?php
/*
*	功能：
*	由不同图片格式，获得图像资源并返回
*/
function getImg($imgName){
	$arr=getimagesize($imgName);
	if($arr[2]==1){
		return imagecreatefromgif($imgName);
	}else if($arr[2]==2){
		return imagecreatefromjpeg($imgName);
	}else if($arr[2]==3){
		return imagecreatefrompng($imgName);
	}else{
		echo "对不起，暂不支持该格式！";
	}
}
 
function output($imgName,$size="medium",$echoText="false"){
	/*
	*	参数说明：
	*	imageName	图像名称
	*	size		可选参数：low、medium、big 功能：控制输出不同的字符画大小
	*	echoText	功能：设置是否保存为txt文件
	*/
 
	$im = getImg($imgName);
	$output="";
	$str='@80GCLft1i;:,. ';	//填充字符
 
	//选取每个像素块的代表点。步长越大，图片越小
	switch($size){
		case "small":
			$stepx=16;
			$stepy=32;
			break;
		case "medium":
			$stepx=4;
			$stempy=8;
			break;
		case "big":
			$stepx=2;
			$stepy=4;
			break;
		default:
			$stepx=4;
			$stempy=8;
			break;
	}
 
	$x=imagesx($im);
	$y=imagesy($im);
	for($j=0;$j<$y;$j+=$stepy){
		for($i=0;$i<$x;$i+=$stepx){
			$colors=imagecolorsforindex($im,imagecolorat($im,$i,$j));	//获取像素块的代表点RGB信息
			$greyness=(0.3*$colors["red"]+0.59*$colors["green"]+0.11*$colors["blue"])/255;	//灰度值计算公式：Gray=R*0.3+G*0.59+B*0.11
			$offset=(int)ceil($greyness*(strlen($str)-1));	//根据灰度值选择合适的字符
			if($offset==(strlen($str)-1))
				$output.=" ";	//替换空格为 ；方便网页输出
			else
				$output.=$str[$offset];
		}
		$output.="<br/>";
	}
 
	imagedestroy($im);
 
	//输出到文本(可选)
	if($echoText != false){
		$output=str_replace("<br/>","\\n",$output);
		$output=str_replace(" "," ",$output);
		@unlink(".\\txt\\".$echoText.".txt");
		file_put_contents(".\\txt\\".$echoText.".txt",$output);
	}else{
    return $output;	//默认输出到网页
  }
	}

  //获取文件列表
function getFile($dir) {
  $fileArray[]=NULL;
  if (false != ($handle = opendir ( $dir ))) {
      $i=0;
      while ( false !== ($file = readdir ( $handle )) ) {
          if ($file != "." && $file != ".."&&strpos($file,".")) {
              $fileArray[$i]=$file;
              $i++;
          }
      }
      closedir ( $handle );
  }
  return $fileArray;
}
	//待转换视频的名字与拓展名
	$videoName = "badapple.mp4";//修改此处即可
  //视频分割5s
  system("ffmpeg -i ". $videoName ." -r 30 -q:v 2 -f image2 ./jpg/%d.jpeg -c .\jpg -t 219");
  //获取图片列表
  $arr = getFile("./jpg");
  //图片转换成文字
  for($i=0;$i<count($arr);$i++){
    $name = $i + 1;
    output("./jpg/".$name.".jpeg", "small", $name);
	}
	$step = 402;//步长
  //文字转换成bas弹幕 3个为1帧 18个为1组 6s 一条弹幕 201 ok 402 ok 804 x
  $header = 'def text c { content = "" fontSize = 2.1% x = 50% y = 50% anchorX = 0.5 anchorY = 0.5 fontFamily = "Courier" }';
  $arr = getFile("./txt");
  for($i=0;$i<count($arr)/$step;$i++){

		$bas = "";
		$m = 0;
    for($j=$i*$step;$j<$step*($i+1);$j++){
			if(($j+1)>count($arr))continue;
      $text = file_get_contents("./txt/".($j+1).".txt");
      if($m == 0){
				$bas = $header.'set c{ content = "'.$text.'"}0.033s ';
				$m = 1;
        continue;
      }
        $bas = $bas.'then set c{ content = "'.$text.'"}0.033s ';
    }
    file_put_contents("./bas/".($i+1).".txt",$bas);
  }

