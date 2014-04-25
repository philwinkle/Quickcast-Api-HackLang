Quickcast API Adapter
==

[Quickcast](http://quickcast.io/) is my favorite screencasting platform. It is a free service that gives content authors a short-form screencasting tool with a hosted platform. While their page/embeds are a nice addition they don't (yet) have a public API.

Until now.

Usage
--

- Include/require the Quickcast class in your project.
- Create a quickcast.json credentials file
- Create a `\Quickcast\Quickcast` class, passing in the `\Quickcast\Config` dependency. Example:
    `$quickcast = new \Quickcast\Quickcast(new \Quickcast\Config());`
- Run one-off via HHVM or daemon. One off example:
    `hhvm example.php`


Todo
--

- Possibly add Composer/Packagist support, but I'm not sure of the process for Hack-only API's.
- Add the ability to specify/configure location of the config json without code modification
- Cache/store token by another means
- Relocate curl cookie file and other pertinents 


Pull Requests
--

Pull requests are welcome. Please modify straight on the master branch and ping me @philwinkle on Twitter if I don't respond.

Written for [HackLang](http://hacklang.org) and tested on HHVM/Hack >= v3.0.1

