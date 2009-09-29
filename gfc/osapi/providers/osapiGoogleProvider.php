<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Pre-defined provider class for Google (www.google.com)
 * @author Chris Chabot
 */
class osapiGoogleProvider extends osapiProvider {
  public $requestTokenParams = array('scope' => 'http://sandbox.gmodules.com/api/people');

  public function __construct(osapiHttpProvider $httpProvider = null) {
    parent::__construct("https://www.google.com/accounts/OAuthGetRequestToken", "https://www.google.com/accounts/OAuthAuthorizeToken", "https://www.google.com/accounts/OAuthGetAccessToken", "http://sandbox.gmodules.com/api", "http://sandbox.gmodules.com/api/rpc", "Google", true, $httpProvider);
  }
}
