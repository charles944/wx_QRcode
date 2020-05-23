<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
class DefaultProfile implements IClientProfile
{
	private $profile;
	private $endpoints;
	private $credential;
	private $regionId;
	private $acceptFormat;
	
	private $isigner;
	private $iCredential;
	
	function  __construct($regionId,$credential)
	{
	    $this->regionId = $regionId;
	    $this->credential = $credential;
	}
	
	public function getProfile($regionId, $accessKeyId, $accessSecret)
	{
		//unset($this->credential);
		$credential =new Credential($accessKeyId, $accessSecret);
		$this->profile = new DefaultProfile($regionId, $credential);
		return $this->profile;
	}
	
	public function getSigner()
	{
		if(null == $this->isigner)
		{
			$this->isigner = new ShaHmac1Signer(); 
		}
		return $this->isigner;
	}
	
	public function getRegionId()
	{
		return $this->regionId;
	}
	
	public function getFormat()
	{
		return $this->acceptFormat;
	}
	
	public function getCredential()
	{
		if(null == $this->credential && null != $this->iCredential)
		{
			$this->credential = $this->iCredential;
		}
		return $this->credential;
	}
	
	public function getEndpoints()
	{
		if(null == $this->endpoints)
		{
			$this->endpoints = EndpointProvider::getEndpoints();
		}
		return $this->endpoints;
	}
	
	public function addEndpoint($endpointName, $regionId, $product, $domain)
	{
		if(null == $this->endpoints)
		{
			$this->endpoints = $this->getEndpoints();
		}
		$endpoint = $this->findEndpointByName($endpointName);
		if(null == $endpoint)
		{
			$this->addEndpoint_($endpointName, $regionId, $product, $domain);
		}
		else 
		{
			$this->updateEndpoint($regionId, $product, $domain, $endpoint);
		}
	}
	
	public function findEndpointByName($endpointName)
	{
		foreach ($this->endpoints as $key => $endpoint)
		{
			if($endpoint->getName() == $endpointName)
			{
				return $endpoint;
			}
		}
	}
	
	private function addEndpoint_($endpointName,$regionId, $product, $domain)
	{
		$regionIds = array($regionId);
		$productsDomains = array(new ProductDomain($product, $domain));
		$endpoint = new Endpoint($endpointName, $regionIds, $productDomains);
		array_push($this->endpoints, $endpoint);
	}
	
	private function updateEndpoint($regionId, $product, $domain, $endpoint)
	{
		$regionIds = $endpoint->getRegionIds();
		if(!in_array($regionId,$regionIds))
		{
			array_push($regionIds, $regionId);
			$endpoint->setRegionIds($regionIds);
		}

		$productDomains = $endpoint->getProductDomains();
		if(null == $this->findProductDomain($productDomains, $product, $domain))
		{
		 	array_push($productDomains, new ProductDomain($product, $domain));	
		}
		$endpoint->setProductDomains($productDomains);
	}
	
	private function findProductDomain($productDomains, $product, $domain)
	{
		foreach ($productDomains as $key => $productDomain)
		{
			if($productDomain->getProductName() == $product && $productDomain->getDomainName() == $domain)
			{
				return $productDomain;
			}
		}
		return null;
	}

}