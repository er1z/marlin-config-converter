# Marlin configuration converter

Face the music - the most popular 3D printer firmware, [Marlin](https://marlinfw.org/), has difficult to maintain
[configuration format](https://marlinfw.org/docs/configuration/configuration.html). Once you make it working, 
it's somewhat common that the configuration is not being touched for months. But, at some point, you need to upgrade
your firmware, and it's highly possible that configuration file will need to be updated as well.

Thankfully, since PlatformIO everything is easier thanks to the schema dumping using `CONFIG_EXPORT` introduced
alongside with [INI configuration support](https://marlinfw.org/docs/configuration/config-ini.html).

Yet it was added in [2.1.1](https://github.com/MarlinFirmware/Marlin/releases/tag/2.1.1). But if you still were on 2.0.x
and for some various reasons you didn't want to update, configuration conversion was not a case.

As I seeked for some side-project to solve totally non-standard problem, here is the tool.

## How it works

First attempt was to process `Configuration.h` and `Configuration_adv.h` using AST parser. It was fine until some weird
macros kicked in - not all configuration switches were extracted due to the fact that those macros actually did some
logic control. But hey, everything worked! Actually it didn't.

At some point, I started examining if it's possible to use standard CPP (preprocessor) to extract all the configuration
values that are a point of interest. Thankfully, it worked!

But another problem arose: number of definitions was rather extensive and target file looked a bit noisy.
So this program does a bit different strategy:

1. Parse supplied `Configuration.h` to determine Marlin version it was configured with
2. Get pristine Marlin distribution matching the version above
3. Copy minion C++ program that does nothing except including definitions from Marlin's `inc/MarlinConfigPre.h`
4. Execute `cpp` with `-dM` flags against the minion, then capture a list of all the defines
5. Copy user configuration to the distribution and re-run `cpp`
6. Make a diff of two lists of defined directives (+ filter out some irrelevant stuff)
7. Dump list in compatible INI format

## Why PHP

Just because. Why. Not?

## Usage

`Configuration.h` is mandatory. `Configuration_adv.h` is optional.

### PHAR

If you have working Linux environment with PHP 8.3 and valid compiler toolchain, get the PHAR file from releases:
```shell
php converter.phar path/Configuration.h [--configuration-adv=path/Configuration_adv.h] > ini-file.ini
```

### Docker

If you don't want to install all the toolchain or are on different platform (like Windows), a Docker image is available:
```shell
docker run --rm \
  -v path/Configuration.h:/Configuration.h \
  -v path/Configuration_adv.h:/Configuration_adv.h \
  er1z/marlin-config-converter:latest \
  /Configuration.h \
  --configuration-adv=/Configuration_adv.h \
> ini-file.ini
```

## Limitations

In contrary to firmware supporting `CONFIG_EXPORT` feature, this tool doesn't distinguish between basic and advanced
configuration switches.

## Disclaimer

This tool may or may not solve your problem. Use it as is, with no warranty. Tried to assure at least acceptable quality
so there is a CI/CD pipeline + automated tests.
