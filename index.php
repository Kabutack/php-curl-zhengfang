<?php 
	header("Content-type: text/html; charset=gbk");
    session_start();
    $id=session_id();
    $_SESSION['id']=$id;
    // echo $_SESSION['id'];
?>
<?php 
    $cookie = dirname(__FILE__) . '/cookie/'.$_SESSION['id'].'.txt';   
    $verify_code_url = "http://210.44.176.46/CheckCode.aspx";//���ѧУ����֤���ַ
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $verify_code_url);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $img = curl_exec($curl);
    curl_close($curl);
    $fp = fopen("/home/wwwroot/default/mail/verifyCode.jpg","w");//��վλ�ã����������
    fwrite($fp,$img);
    fclose($fp);
    $shibie_code_url='http://www.kejibu.org:8080/WhxyJw/yzm.jsp?c=&url=http://115.159.53.241/mail/verifyCode.jpg';//��֤������ʶ�𣬸�л��˾�����������http://www.unique-liu.com/211.html
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $shibie_code_url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec($curl);
    curl_close($curl);
    
    $re=trim($re);
    // echo $re;
    function login_post($url,$cookie,$post){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); 
        curl_setopt($ch, CURLOPT_REFERER, 'http://210.44.176.46/'); //REFERER�ĳ�����ѧУ����ϵͳ
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    $url="http://210.44.176.46/default2.aspx";//���ѧУ����ϵͳ��ַ
    $con1=login_post($url,$cookie,'');
    preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $con1, $view);
    $post=array(
        '__VIEWSTATE'=>$view[1][0],
        'txtUserName'=>'13XXXXX',//ѧ��
        'TextBox2'=>'XXXXX',//����
        'txtSecretCode'=>$re,//��֤��
        'RadioButtonList1'=>'%D1%A7%C9%FA',
        'Button1'=>'',
        'lbLanguage'=>'',
        'hidPdrs'=>'',
        'hidsc'=>''
    );
    // var_dump($post);
    $con2=login_post($url,$cookie,http_build_query($post));
    // echo $con2;

    preg_match_all('/<span id="xhxm">([^<>]+)/', $con2, $xm);
    $xm[1][0]=substr($xm[1][0],0,-4);
    // echo $xm[1][0];
    $url2="http://210.44.176.46/xscjcx.aspx?xh="."13XXXXX"."&xm=".$xm[1][0]; //�ɼ�����URL

    $viewstate=login_post($url2,$cookie,'');
    preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $viewstate, $vs);
    // var_dump($vs);
    $state=$vs[1][0];
    $post=array(
        '__EVENTTARGET'=>'',
        '__EVENTARGUMENT'=>'',
        '__VIEWSTATE'=>$state,
        'hidLanguage'=>'',
        'ddlXN'=>'2015-2016',
        'ddlXQ'=>'2',
        'ddl_kcxz'=>'',
        'btn_xq'=>'%D1%A7%C6%DA%B3%C9%BC%A8'
    );

    // var_dump($post);
    $content=login_post($url2,$cookie,http_build_query($post));
    preg_match_all('/<td>([^<>]+)/', $content, $cj);
    $num=count($cj[1]);//91
    echo $num;//����<td>������
    if($num>143){
        //���ʼ�
        require 'PHPMailerAutoload.php';                 //PHPMAIL����

        $mail = new PHPMailer;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();   
        $mail->CharSet = "UTF-8";  //�ַ���
        $mail->setLanguage('zh_cn','/language');

        // Set mailer to use SMTP
        $mail->Host = 'smtp.163.com';  // Specify main and backup SMTP servers //���������
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'admin@admin.com';                 // SMTP username 
        $mail->Password = 'password';                           // SMTP password //����������
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to

        $mail->setFrom('admin@admin.com', 'GJJ');        //������������ǳ�
        // $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
        $mail->addAddress('1392552862@qq.com');               // Name is optional //�ռ�������
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = '�߼ξ�ͬѧ';  //����
        $mail->Body = '���ɼ���';  //����
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            echo '�ʼ�δ����';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            echo '<br>';
        } else {
            echo '�ʼ��ѷ���'.'<br>';
        }
    }
    else{
        echo "δץ���ɼ�"."<br>";
    }
    $file="/home/wwwroot/default/mail/verifyCode.jpg";
    unlink($file);//ɾ����֤��

?>