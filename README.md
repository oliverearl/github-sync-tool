[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]
<br>
[![PHP Compilation](https://github.com/oliverearl/github-sync-tool/actions/workflows/build.yml/badge.svg)](https://github.com/oliverearl/github-sync-tool/actions/workflows/build.yml)
[![PHP Tests](https://github.com/oliverearl/github-sync-tool/actions/workflows/tests.yml/badge.svg)](https://github.com/oliverearl/github-sync-tool/actions/workflows/tests.yml)
[![PHP Linting](https://github.com/oliverearl/github-sync-tool/actions/workflows/pint.yml/badge.svg)](https://github.com/oliverearl/github-sync-tool/actions/workflows/pint.yml)

<div align="center">
    <h1 align="center">GitHub Synchronisation Tool</h1>
    <p align="center">
        A tool for synchronising commits across GitHub profiles
    </p>
</div>

## About The Project

The project came about as a desire to sync the contribution graph between my GitHub accounts. I'm required to use a different, private GitHub account for work, and I figured I should be able to show off the hard work that I carry out everyday on my public profile. Enter this tool.

This tool will scrape your public GitHub profile (so make sure those contributions are visible!) and build a Bash script to either be run within this repository or at your leisure to historically build a series of commits associated to you, so you can populate your graph in the same way.

This program was written using the amazing Laravel Zero - check `laravel-zero.md` for more information.

## Getting Started

This application is compiled into a standalone executable, but you'll still need the following to run it:

- PHP 8.1 or above
- PHP-DOM and PHP-CURL extensions

It has been tested on both Windows and the latest version of Ubuntu, but it should work on other platforms provided the above prerequisites are met.

You can download the latest release from the releases section of this repository.

### From Source

You can alternatively clone this repository and run the uncompiled version of the application, or compile it yourself. You will need [Composer](https://getcomposer.org) to install the dependencies:

1. Clone this repository to your computer (or use a tool of your choice):

```bash
  git clone https://github.com/oliverearl/github-sync-tool.git
```

2. Navigate into the directory and install dependencies using Composer: 

```bash
cd github-sync-tool
composer install
```

3. View the list of available commands by invoking the program:

```bash
./gh-sync 
```

4. (Optional) You can compile the program into an executable with the following:

```bash
./gh-sync app:build
```

### Note for Windows users (non-WSL users)

The only limitation is that the application cannot natively execute the script since it's written in Bash, and this application assumes that you're unable to execute it.

## Usage

The program takes two parameters:

```bash
./gh-sync oliverearl 2022
```

The first parameter is the GitHub account username that you want to pull contributions from. Ensure that it is visible even when logged out. Do not include the '@' symbol or the full web address or an error will be displayed.

On running the program, you will be prompted to provide a name and email address to associate with your Git commits. The program won't continue without them as it does not want to make any assumptions.

**Important**: Make sure you choose an email address associated with your GitHub account or this won't work!

Next, after the program has enumerated the contributions, it will ask you if its findings look correct and that you want to proceed. If so, it'll write a Bash script to the current working directory containing the username of the targeted GitHub account.

Lastly, if you're not running on Windows, it will ask you whether you want to immediately execute this script in the current directory. This assumes that the script is running in a Git repository. If you're not sure, choose no, and run the script manually in the directory of your choosing.

Once the script has run, you will find that the current Git repository has been populated with commits. You can push these to GitHub, where you'll have your contribution graph updated, though please bear in mind this can take up to 24 hours.

## What's planned?
Upcoming features planned include:
- Proper documentation and an updated README! That's my first priority.

- Feature tests for the entire process, as they're still in-progress.

- Accepting a range of dates as a parameter, or automatically looking for all dates.

- Supporting other platforms, namely GitLab and Bitbucket but open to other suggestions.

- Supporting exports to Batch or PowerShell as alternatives to Bash scripts.

## Contributing

If you have a suggestion that would make this better, please fork the repo and create a pull request.

Tests, bugfixes, and documentation are especially welcome.

New functionality **must include** relevant tests. You can write these in PHPUnit or Pest, but be consistent.

Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE.md` for more information.

## Acknowledgments

- [@kefimochi](https://github.com/kefimochi/sync-contribution-graph) for inspiring me to create this tool, as they have provided a similar solution written in Node.

- [@nunomaduro and @owenvoke](https://github.com/laravel-zero/laravel-zero) for producing the amazing Laravel Zero framework, as well as Pest which is used here for testing!

- [@taylorotwell](https://github.com/laravel/laravel) for the amazing Laravel framework.

- [@othneildrew](https://github.com/othneildrew/Best-README-Template) for providing the useful README template.

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/oliverearl/github-sync-tool.svg?style=for-the-badge
[contributors-url]: https://github.comoliverearl/github-sync-tool/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/oliverearl/github-sync-tool.svg?style=for-the-badge
[forks-url]: https://github.com/oliverearl/github-sync-tool/network/members
[stars-shield]: https://img.shields.io/github/stars/oliverearl/github-sync-tool.svg?style=for-the-badge
[stars-url]: https://github.com/othneildrew/Best-README-Template/stargazers
[issues-shield]: https://img.shields.io/github/issues/oliverearl/github-sync-tool?style=for-the-badge
[issues-url]: https://github.com/othneildrew/Best-README-Template/issues
[license-shield]: https://img.shields.io/github/license/oliverearl/github-sync-tool?style=for-the-badge
[license-url]: https://github.com/oliverearl/github-sync-tool/blob/master/LICENSE.md
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/oliverearl
