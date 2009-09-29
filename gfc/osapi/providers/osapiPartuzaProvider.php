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
 * Pre-defined provider class for Partuza (partuza)
 * @author Chris Chabot
 */
class osapiPartuzaProvider extends osapiProvider {
  public function __construct(osapiHttpProvider $httpProvider = null) {
    parent::__construct("http://www.partuza.nl/oauth/request_token", "http://www.partuza.nl/oauth/authorize", "http://www.partuza.nl/oauth/access_token", "http://modules.partuza.nl/social/rest", "http://modules.partuza.nl/social/rpc", "Partuza", true, $httpProvider);
  }
}

/**
 * Class for local debugging and development
 */
class osapiLocalPartuzaProvider extends osapiProvider {
  public function __construct(osapiHttpProvider $httpProvider = null) {
    parent::__construct("http://partuza/oauth/request_token", "http://partuza/oauth/authorize", "http://partuza/oauth/access_token", "http://shindig/social/rest", "http://shindig/social/rpc", "LocalPartuza", true, $httpProvider);
  }
}
