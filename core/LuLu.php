<?php

namespace app\core;

use yii\helpers\VarDumper;
class LuLu extends \Yii
{

	public static function getApp()
	{
		return self::$app;
	}

	public static function getView()
	{
		$app = self::getApp();
		return $app->getView();
	}

	public static function getRequest()
	{
		$app = self::getApp();
		return $app->request;
	}

	public static function getResponse()
	{
		$app = static::getApp();
		return $app->response;
	}

	public static function getBaseUrl($url = null)
	{
		$app = self::getApp();
		$baseUrl = $app->request->getBaseUrl();
		if($url !== null)
		{
			$baseUrl .= $url;
		}
		return $baseUrl;
	}

	public static function getHomeUrl($url = null)
	{
		$app = self::getApp();
		$homeUrl = $app->getHomeUrl();
		if($url !== null)
		{
			$homeUrl .= $url;
		}
		return $homeUrl;
	}

	public static function getWebUrl($url = null)
	{
		$webUrl = self::getAlias('@web');
		if($url !== null)
		{
			$webUrl .= $url;
		}
		return $webUrl;
	}

	public static function getWebPath($path = null)
	{
		$webPath = self::getAlias('@webroot');
		if($path !== null)
		{
			$webPath .= $path;
		}
		return $webPath;
	}

	public static function getAppParam($key, $defaultValue = null)
	{
		$app = self::getApp();
		if(array_key_exists($key, $app->params))
		{
			return $app->params[$key];
		}
		return $defaultValue;
	}

	public static function setAppParam($array)
	{
		$app = self::getApp();
		foreach($array as $key => $value)
		{
			$app->params[$key] = $value;
		}
	}

	public static function getViewParam($key, $defaultValue = null)
	{
		$app = self::getApp();
		$view = $app->getView();
		if(array_key_exists($key, $view->params[$key]))
		{
			return $view->params[$key];
		}
		return $defaultValue;
	}

	public static function setViewParam($array)
	{
		$app = self::getApp();
		$view = $app->getView();
		foreach($array as $name => $value)
		{
			$view->params[$name] = $value;
		}
	}

	public static function hasGetValue($key)
	{
		return isset($_GET[$key]);
	}

	public static function getGetValue($key, $default = NULL)
	{
		if(self::hasGetValue($key))
		{
			return $_GET[$key];
		}
		return $default;
	}

	public static function hasPostValue($key)
	{
		return isset($_POST[$key]);
	}

	public static function getPostValue($key, $default = NULL)
	{
		if(self::hasPostValue($key))
		{
			return $_POST[$key];
		}
		return $default;
	}

	public static function setFalsh($type, $message)
	{
		$app = self::getApp();
		$app->session->setFlash($type, $message);
	}

	public static function setWarningMessage($message)
	{
		$app = self::getApp();
		$app->session->setFlash('warning', $message);
	}

	public static function setSuccessMessage($message)
	{
		$app = self::getApp();
		$app->session->setFlash('success', $message);
	}

	public static function setErrorMessage($message)
	{
		$app = self::getApp();
		$app->session->setFlash('error', $message);
	}

	public static function info($var, $category = 'application')
	{
		
		$dump = VarDumper::dumpAsString($var);
		self::info($dump, $category);
	}

	public static function getUser()
	{
		$app = self::getApp();
		return $app->user;
	}

	public static function getIdentity()
	{
		$app = self::getApp();
		return $app->user->getIdentity();
	}

	public static function getIsGuest()
	{
		$app = self::getApp();
		return $app->user->isGuest;
	}

	public static function checkIsGuest($loginUrl = null)
	{
		$app = self::getApp();
		$isGuest = $app->user->isGuest;
		if($isGuest)
		{
			if($loginUrl == false)
			{
				return false;
			}
			if($loginUrl == null)
			{
				$loginUrl = ['site/login'];
			}
			return $app->getResponse()->redirect(Url::to($loginUrl), 302);
		}
		return true;
	}

	public static function checkAuth($permissionName, $params = [], $allowCaching = true)
	{
		$app = self::getApp();
		return $app->user->can($permissionName, $params, $allowCaching);
	}

	public static function getDB()
	{
		$app = self::getApp();
		return $app->db;
	}

	public static function createCommand($sql = null)
	{
		$app = self::getApp();
		$db = $app->db;
		if($sql !== null)
		{
			return $db->createCommand($sql);
		}
		return $db->createCommand();
	}

	public static function execute($sql)
	{
		$app = self::getApp();
		$db = $app->db;
		$command = $db->createCommand($sql);
		return $command->execute();
	}

	public static function queryAll($sql)
	{
		$app = self::getApp();
		$db = $app->db;
		$command = $db->createCommand($sql);
		return $command->queryAll();
	}

	public static function queryOne($sql)
	{
		$app = self::getApp();
		$db = $app->db;
		$command = $db->createCommand($sql);
		return $command->queryOne();
	}

	public static function getPagedRows($query, $config = [])
	{
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		if(isset($config['page']))
		{
			$pages->setPage($config['page'], true);
		}
		
		if(isset($config['pageSize']))
		{
			$pages->setPageSize($config['pageSize'], true);
		}
		
		$rows = $query->offset($pages->offset)->limit($pages->limit);
		
		if(isset($config['order']))
		{
			$rows = $rows->orderBy($config['order']);
		}
		$rows = $rows->all();
		
		$rowsLable = 'rows';
		$pagesLable = 'pages';
		
		if(isset($config['rows']))
		{
			$rowsLable = $config['rows'];
		}
		if(isset($config['pages']))
		{
			$pagesLable = $config['pages'];
		}
		
		$ret = [];
		$ret[$rowsLable] = $rows;
		$ret[$pagesLable] = $pages;
		
		return $ret;
	}
}
