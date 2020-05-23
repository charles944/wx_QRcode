<?php
namespace common\model;
use think\Model;

/**
 * ����ģ��
 */
class ChineseModel extends Model
{

	/**
	 * ��ż���������ƴ�����ձ�
	 *
	 * @��������  ����
	 * @��ʼ      1.0
	 * @����޸�  1.0
	 * @����      �ڲ�
	 */
	var $pinyin_table = array();

	
	/**
	 * ��� GB <-> UNICODE ���ձ������
	 * @��������  
	 * @��ʼ      1.1
	 * @����޸�  1.2
	 * @����      �ڲ�
	 */
	var $unicode_table = array();

	/**
	 * �������ķ��򻥻�����ļ�ָ��
	 *
	 * @��������  ����
	 * @��ʼ      1.0
	 * @����޸�  1.0
	 * @����      �ڲ�
	 */
	var $ctf;

	/**
	 * �ȴ�ת�����ַ���
	 * @��������
	 * @��ʼ      1.0
	 * @����޸�  1.0
	 * @����      �ڲ�
	 */
	var $SourceText = "";

	/**
	 * Chinese ����������
	 *
	 * @��������  ����
	 * @��ʼ      1.0
	 * @����޸�  1.2
	 * @����      ����
	 */
	var $config  =  array(
		'codetable_dir'         => "./config/",           //  ��Ÿ������Ի������Ŀ¼
		'SourceLang'            => '',                    //  �ַ���ԭ����
		'TargetLang'            => '',                    //  ת����ı���
		'GBtoBIG5_table'        => 'gb-big5.table',       //  ��������ת��Ϊ�������ĵĶ��ձ�
		'BIG5toGB_table'        => 'big5-gb.table',       //  ��������ת��Ϊ�������ĵĶ��ձ�
		'GBtoPinYin_table'      => 'gb-pinyin.table',     //  ��������ת��Ϊƴ���Ķ��ձ�
		'GBtoUnicode_table'     => 'gb-unicode.table',    //  ��������ת��ΪUNICODE�Ķ��ձ�
		'BIG5toUnicode_table'   => 'big5-unicode.table'   //  ��������ת��ΪUNICODE�Ķ��ձ�
	);

	/**
	 * Chinese ��Ϥ������
	 *
	 * ��ϸ˵��
	 * @�β�      �ַ��� $SourceLang Ϊ��Ҫת�����ַ�����ԭ����
	 *            �ַ��� $TargetLang Ϊת����Ŀ�����
	 *            �ַ��� $SourceText Ϊ�ȴ�ת�����ַ���
	 *            �ַ��� $CodetableDir �����Ӧ���Ŀ¼
	 *
	 * @��ʼ      1.0
	 * @����޸�  1.2
	 * @����      ����
	 * @����ֵ    ��
	 * @throws
	 */
	public function Chinese( $SourceLang , $TargetLang , $SourceString='', $CodetableDir='')
	{
		if ($SourceLang != '') {
		    $this->config['SourceLang'] = $SourceLang;
		}

		if ($TargetLang != '') {
		    $this->config['TargetLang'] = $TargetLang;
		}

		if ($SourceString != '') {
		    $this->SourceText = $SourceString;
		}

		// codes Added by Zeal Li on ver 1.6 begin.
		if ($CodetableDir != '') {
			if(! is_dir($CodetableDir)){
				echo "configuration directory [".$CodetableDir."] not exists!";
				exit;
			}
		    $this->config['codetable_dir'] = $CodetableDir;
		}
		// codes Added by Zeal Li on ver 1.6 end.

		$this->OpenTable();
	} // ���� Chinese ��Ϥ������


	/**
	 * �� 16 ����ת��Ϊ 2 �����ַ�
	 *
	 * ��ϸ˵��
	 * @�β�      $hexdata Ϊ16���Ƶı���
	 * @��ʼ      1.5
	 * @����޸�  1.5
	 * @����      �ڲ�
	 * @����      �ַ���
	 * @throws    
	 */
	function _hex2bin( $hexdata )
	{
		for ( $i=0; $i<strlen($hexdata); $i+=2 )
			$bindata.=chr(hexdec(substr($hexdata,$i,2)));

		return $bindata;
	}


	/**
	 * �򿪶��ձ�
	 *
	 * ��ϸ˵��
	 * @�β�      
	 * @��ʼ      1.3
	 * @����޸�  1.3
	 * @����      �ڲ�
	 * @����      ��
	 * @throws    
	 */
	function OpenTable()
	{
	    
		// ����ԭ����Ϊ�������ĵĻ�
		if ($this->config['SourceLang']=="GB2312") {

			// ����ת��Ŀ�����Ϊ�������ĵĻ�
			if ($this->config['TargetLang'] == "BIG5") {
				$this->ctf = fopen($this->config['codetable_dir'].$this->config['GBtoBIG5_table'], "r");
				if (is_null($this->ctf)) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
			}

			// ����ת��Ŀ�����Ϊƴ���Ļ�
			if ($this->config['TargetLang'] == "PinYin") {
				$tmp = @file($this->config['codetable_dir'].$this->config['GBtoPinYin_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				//
				$i = 0;
				for ($i=0; $i<count($tmp); $i++) {
					$tmp1 = explode("	", $tmp[$i]);
					$this->pinyin_table[$i]=array($tmp1[0],$tmp1[1]);
				}
			}

			// ����ת��Ŀ�����Ϊ UTF8 �Ļ�
			if ($this->config['TargetLang'] == "UTF8") {
				$tmp = @file($this->config['codetable_dir'].$this->config['GBtoUnicode_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				$this->unicode_table = array();
				while(list($key,$value)=each($tmp))
					$this->unicode_table[hexdec(substr($value,0,6))]=substr($value,7,6);
			}

			// ����ת��Ŀ�����Ϊ UNICODE �Ļ�
			if ($this->config['TargetLang'] == "UNICODE") {
				$tmp = @file($this->config['codetable_dir'].$this->config['GBtoUnicode_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				$this->unicode_table = array();
				while(list($key,$value)=each($tmp))
					$this->unicode_table[hexdec(substr($value,0,6))]=substr($value,9,4);
			}
		}

		// ����ԭ����Ϊ�������ĵĻ�
		if ($this->config['SourceLang']=="BIG5") {
			// ����ת��Ŀ�����Ϊ�������ĵĻ�
			if ($this->config['TargetLang'] == "GB2312") {
				$this->ctf = fopen($this->config['codetable_dir'].$this->config['BIG5toGB_table'], "r");
				if (is_null($this->ctf)) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
			}
			// ����ת��Ŀ�����Ϊ UTF8 �Ļ�
			if ($this->config['TargetLang'] == "UTF8") {
				$tmp = @file($this->config['codetable_dir'].$this->config['BIG5toUnicode_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				$this->unicode_table = array();
				while(list($key,$value)=each($tmp))
					$this->unicode_table[hexdec(substr($value,0,6))]=substr($value,7,6);
			}

			// ����ת��Ŀ�����Ϊ UNICODE �Ļ�
			if ($this->config['TargetLang'] == "UNICODE") {
				$tmp = @file($this->config['codetable_dir'].$this->config['BIG5toUnicode_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				$this->unicode_table = array();
				while(list($key,$value)=each($tmp))
					$this->unicode_table[hexdec(substr($value,0,6))]=substr($value,9,4);
			}

			// ����ת��Ŀ�����Ϊƴ���Ļ�
			if ($this->config['TargetLang'] == "PinYin") {
				$tmp = @file($this->config['codetable_dir'].$this->config['GBtoPinYin_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				//
				$i = 0;
				for ($i=0; $i<count($tmp); $i++) {
					$tmp1 = explode("	", $tmp[$i]);
					$this->pinyin_table[$i]=array($tmp1[0],$tmp1[1]);
				}
			}
		}

		// ����ԭ����Ϊ UTF8 �Ļ�
		if ($this->config['SourceLang']=="UTF8") {

			// ����ת��Ŀ�����Ϊ GB2312 �Ļ�
			if ($this->config['TargetLang'] == "GB2312") {
				$tmp = @file($this->config['codetable_dir'].$this->config['GBtoUnicode_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				$this->unicode_table = array();
				while(list($key,$value)=each($tmp))
					$this->unicode_table[hexdec(substr($value,7,6))]=substr($value,0,6);
			}

			// ����ת��Ŀ�����Ϊ BIG5 �Ļ�
			if ($this->config['TargetLang'] == "BIG5") {
				$tmp = @file($this->config['codetable_dir'].$this->config['BIG5toUnicode_table']);
				if (!$tmp) {
					echo "�򿪴�ת�����ļ�ʧ�ܣ�";
					exit;
				}
				$this->unicode_table = array();
				while(list($key,$value)=each($tmp))
					$this->unicode_table[hexdec(substr($value,7,6))]=substr($value,0,6);
			}
		}

	} // ���� OpenTable ����

	/**
	 * �򿪱��ػ���Զ�̵��ļ�
	 *
	 * ��ϸ˵��
	 * @�β�      �ַ��� $position Ϊ��Ҫ�򿪵��ļ����ƣ�֧�ִ�·����URL
	 *            ����ֵ $isHTML Ϊ��ת�����ļ��Ƿ�Ϊhtml�ļ�
	 * @��ʼ      1.1
	 * @����޸�  1.1
	 * @����      ����
	 * @����      ��
	 * @throws    
	 */
	function OpenFile( $position , $isHTML=false )
	{
	    $tempcontent = @file($position);

		if (!$tempcontent) {
		    echo "���ļ�ʧ�ܣ�";
			exit;
		}

		$this->SourceText = implode("",$tempcontent);

		if ($isHTML) {
			$this->SourceText = eregi_replace( "charset=".$this->config['SourceLang'] , "charset=".$this->config['TargetLang'] , $this->SourceText);

			$this->SourceText = eregi_replace("\n", "", $this->SourceText);

			$this->SourceText = eregi_replace("\r", "", $this->SourceText);
		}
	} // ���� OpenFile ����

	/**
	 * �򿪱��ػ���Զ�̵��ļ�
	 *
	 * ��ϸ˵��
	 * @�β�      �ַ��� $position Ϊ��Ҫ�򿪵��ļ����ƣ�֧�ִ�·����URL
	 * @��ʼ      1.1
	 * @����޸�  1.1
	 * @����      ����
	 * @����      ��
	 * @throws    
	 */
	function SiteOpen( $position )
	{
	    $tempcontent = @file($position);

		if (!$tempcontent) {
		    echo "���ļ�ʧ�ܣ�";
			exit;
		}

		// ���������������ת��Ϊ�ַ���
		$this->SourceText = implode("",$tempcontent);

		$this->SourceText = eregi_replace( "charset=".$this->config['SourceLang'] , "charset=".$this->config['TargetLang'] , $this->SourceText);


//		ereg(href="css/dir.css"
	} // ���� OpenFile ����

	/**
	 * ���ñ�����ֵ
	 *
	 * ��ϸ˵��
	 * @�β�
	 * @��ʼ      1.0
	 * @����޸�  1.0
	 * @����      ����
	 * @����ֵ    ��
	 * @throws
	 */
	function setvar( $parameter , $value )
	{
		if(!trim($parameter))
			return $parameter;

		$this->config[$parameter] = $value;

	} // ���� setvar ����

	/**
	 * �����塢�������ĵ� UNICODE ����ת��Ϊ UTF8 �ַ�
	 *
	 * ��ϸ˵��
	 * @�β�      ���� $c �������ĺ��ֵ�UNICODE�����10����
	 * @��ʼ      1.1
	 * @����޸�  1.2
	 * @����      �ڲ�
	 * @����      �ַ���
	 * @throws    
	 */
	function CHSUtoUTF8($c)
	{
		$str="";

		if ($c < 0x80) {
			$str.=$c;
		}

		else if ($c < 0x800) {
			$str.=(0xC0 | $c>>6);
			$str.=(0x80 | $c & 0x3F);
		}

		else if ($c < 0x10000) {
			$str.=(0xE0 | $c>>12);
			$str.=(0x80 | $c>>6 & 0x3F);
			$str.=(0x80 | $c & 0x3F);
		}

		else if ($c < 0x200000) {
			$str.=(0xF0 | $c>>18);
			$str.=(0x80 | $c>>12 & 0x3F);
			$str.=(0x80 | $c>>6 & 0x3F);
			$str.=(0x80 | $c & 0x3F);
		}

		return $str;
	} // ���� CHSUtoUTF8 ����
	
	/**
	 * ���塢�������� <-> UTF8 ����ת���ĺ���
	 *
	 * ��ϸ˵��
	 * @�β�      
	 * @��ʼ      1.1
	 * @����޸�  1.5
	 * @����      �ڲ�
	 * @����      �ַ���
	 * @throws    
	 */
	function CHStoUTF8(){

		if ($this->config["SourceLang"]=="BIG5" || $this->config["SourceLang"]=="GB2312") {
			$ret="";

			while($this->SourceText != ""){

				if(ord(substr($this->SourceText,0,1))>127){

					if ($this->config["SourceLang"]=="BIG5") {
						$utf8=$this->CHSUtoUTF8(hexdec($this->unicode_table[hexdec(bin2hex(substr($this->SourceText,0,2)))]));
					}
					if ($this->config["SourceLang"]=="GB2312") {
						$utf8=$this->CHSUtoUTF8(hexdec($this->unicode_table[hexdec(bin2hex(substr($this->SourceText,0,2)))-0x8080]));
					}
					for($i=0;$i<strlen($utf8);$i+=3)
						$ret.=chr(substr($utf8,$i,3));

					$this->SourceText=substr($this->SourceText,2,strlen($this->SourceText));
				}
				
				else{
					$ret.=substr($this->SourceText,0,1);
					$this->SourceText=substr($this->SourceText,1,strlen($this->SourceText));
				}
			}
			$this->unicode_table = array();
			$this->SourceText = "";
			return $ret;
		}

		if ($this->config["SourceLang"]=="UTF8") {
			$out = "";
			$len = strlen($this->SourceText);
			$i = 0;
			while($i < $len) {
				$c = ord( substr( $this->SourceText, $i++, 1 ) );
				switch($c >> 4)
				{ 
					case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
						// 0xxxxxxx
						$out .= substr( $this->SourceText, $i-1, 1 );
					break;
					case 12: case 13:
						// 110x xxxx   10xx xxxx
						$char2 = ord( substr( $this->SourceText, $i++, 1 ) );
						$char3 = $this->unicode_table[(($c & 0x1F) << 6) | ($char2 & 0x3F)];

						if ($this->config["TargetLang"]=="GB2312")
							$out .= $this->_hex2bin( dechex(  $char3 + 0x8080 ) );

						if ($this->config["TargetLang"]=="BIG5")
							$out .= $this->_hex2bin( $char3 );
					break;
					case 14:
						// 1110 xxxx  10xx xxxx  10xx xxxx
						$char2 = ord( substr( $this->SourceText, $i++, 1 ) );
						$char3 = ord( substr( $this->SourceText, $i++, 1 ) );
						$char4 = $this->unicode_table[(($c & 0x0F) << 12) | (($char2 & 0x3F) << 6) | (($char3 & 0x3F) << 0)];

						if ($this->config["TargetLang"]=="GB2312")
							$out .= $this->_hex2bin( dechex ( $char4 + 0x8080 ) );

						if ($this->config["TargetLang"]=="BIG5")
							$out .= $this->_hex2bin( $char4 );
					break;
				}
			}

			// ���ؽ��
			return $out;
		}
	} // ���� CHStoUTF8 ����

	/**
	 * ���塢��������ת��Ϊ UNICODE����
	 *
	 * ��ϸ˵��
	 * @�β�      
	 * @��ʼ      1.2
	 * @����޸�  1.2
	 * @����      �ڲ�
	 * @����      �ַ���
	 * @throws    
	 */
	function CHStoUNICODE()
	{

		$utf="";

		while($this->SourceText != "")
		{
			if (ord(substr($this->SourceText,0,1))>127)
			{

				if ($this->config["SourceLang"]=="GB2312")
					$utf.="&#x".$this->unicode_table[hexdec(bin2hex(substr($this->SourceText,0,2)))-0x8080].";";

				if ($this->config["SourceLang"]=="BIG5")
					$utf.="&#x".$this->unicode_table[hexdec(bin2hex(substr($this->SourceText,0,2)))].";";

				$this->SourceText=substr($this->SourceText,2,strlen($this->SourceText));
			}
			else
			{
				$utf.=substr($this->SourceText,0,1);
				$this->SourceText=substr($this->SourceText,1,strlen($this->SourceText));
			}
		}
		return $utf;
	} // ���� CHStoUNICODE ����

	/**
	 * �������� <-> �������� ����ת���ĺ���
	 *
	 * ��ϸ˵��
	 * @��ʼ      1.0
	 * @����      �ڲ�
	 * @����ֵ    ���������utf8�ַ�
	 * @throws
	 */
	function GB2312toBIG5()
	{
		// ��ȡ�ȴ�ת�����ַ������ܳ���
		$max=strlen($this->SourceText)-1;

		for($i=0;$i<$max;$i++){

			$h=ord($this->SourceText[$i]);

			if($h>=160){

				$l=ord($this->SourceText[$i+1]);

				if($h==161 && $l==64){
					$gb="  ";
				}
				else{
					fseek($this->ctf,($h-160)*510+($l-1)*2);
					$gb=fread($this->ctf,2);
				}

				$this->SourceText[$i]=$gb[0];
				$this->SourceText[$i+1]=$gb[1];
				$i++;
			}
		}
		fclose($this->ctf);

		// ��ת����Ľ������ $result;
		$result = $this->SourceText;

		// ��� $thisSourceText
		$this->SourceText = "";

		// ����ת�����
		return $result;
	} // ���� GB2312toBIG5 ����

	/**
	 * �������õ��ı�����Ѱƴ��
	 *
	 * ��ϸ˵��
	 * @��ʼ      1.0
	 * @����޸�  1.0
	 * @����      �ڲ�
	 * @����ֵ    �ַ���
	 * @throws
	 */
	function PinYinSearch($num){

		if($num>0&&$num<160){
			return chr($num);
		}

		elseif($num<-20319||$num>-10247){
			return "";
		}

		else{

			for($i=count($this->pinyin_table)-1;$i>=0;$i--){
				if($this->pinyin_table[$i][1]<=$num)
					break;
			}

			return $this->pinyin_table[$i][0];
		}
	} // ���� PinYinSearch ����

	/**
	 * ���塢�������� -> ƴ�� ת��
	 *
	 * ��ϸ˵��
	 * @��ʼ      1.0
	 * @����޸�  1.3
	 * @����      �ڲ�
	 * @����ֵ    �ַ�����ÿ��ƴ���ÿո�ֿ�
	 * @throws
	 */
	function CHStoPinYin(){
		if ( $this->config['SourceLang']=="BIG5" ) {
			$this->ctf = fopen($this->config['codetable_dir'].$this->config['BIG5toGB_table'], "r");
			if (is_null($this->ctf)) {
				echo "�򿪴�ת�����ļ�ʧ�ܣ�";
				exit;
			}

			$this->SourceText = $this->GB2312toBIG5();
			$this->config['TargetLang'] = "PinYin";
		}

		$ret = array();
		$ri = 0;
		for($i=0;$i<strlen($this->SourceText);$i++){

			$p=ord(substr($this->SourceText,$i,1));

			if($p>160){
				$q=ord(substr($this->SourceText,++$i,1));
				$p=$p*256+$q-65536;
			}

			$ret[$ri]=$this->PinYinSearch($p);
			$ri = $ri + 1;
		}

		// ��� $this->SourceText
		$this->SourceText = "";

		$this->pinyin_table = array();

		// ����ת����Ľ��
		return implode(" ", $ret);
	} // ���� CHStoPinYin ����

	/**
	 * ���ת�����
	 *
	 * ��ϸ˵��
	 * @�β�
	 * @��ʼ      1.0
	 * @����޸�  1.2
	 * @����      ����
	 * @����      �ַ���
	 * @throws
	 */
	public function ConvertIT()
	{
		// �ж��Ƿ�Ϊ���ķ�����ת��
		if ( ($this->config['SourceLang']=="GB2312" || $this->config['SourceLang']=="BIG5") && ($this->config['TargetLang']=="GB2312" || $this->config['TargetLang']=="BIG5") ) {
			return $this->GB2312toBIG5();
		}

		// �ж��Ƿ�Ϊ����������ƴ��ת��
		if ( ($this->config['SourceLang']=="GB2312" || $this->config['SourceLang']=="BIG5") && $this->config['TargetLang']=="PinYin" ) {
			return $this->CHStoPinYin();
		}

		// �ж��Ƿ�Ϊ���塢����������UTF8ת��
		if ( ($this->config['SourceLang']=="GB2312" || $this->config['SourceLang']=="BIG5" || $this->config['SourceLang']=="UTF8") && ($this->config['TargetLang']=="UTF8" || $this->config['TargetLang']=="GB2312" || $this->config['TargetLang']=="BIG5") ) {
			return $this->CHStoUTF8();
		}

		// �ж��Ƿ�Ϊ���塢����������UNICODEת��
		if ( ($this->config['SourceLang']=="GB2312" || $this->config['SourceLang']=="BIG5") && $this->config['TargetLang']=="UNICODE" ) {
			return $this->CHStoUNICODE();
		}

	} // ���� ConvertIT ����

} // �������

/**
*/

?>
