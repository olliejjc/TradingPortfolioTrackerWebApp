<a name="readme-top"></a>

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/github_username/repo_name">
    <img src="public/image/logo.png" alt="Logo" width="80" height="80">
  </a>

<h3 align="center">Trading Portfolio Tracker Web App Readme</h3>

  <p align="center">
    Store and track your trading history, view your performance over time!
    <br />
    <a href="https://github.com/olliejjc/TradingPortfolioTrackerWebApp"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/olliejjc/TradingPortfolioTrackerWebApp/issues">Report Bug</a>
    ·
    <a href="https://github.com/olliejjc/TradingPortfolioTrackerWebApp/issues">Request Feature</a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

![Product Name Screen Shot][product-screenshot]

The Trading Portfolio Tracker App enables users to track the performance of their trading portfolio over multiple periods of time. Each user can create an account, set their portfolio size and their risk percentage per trade. When the user makes a trade they can add the details of the trade they took into the Trading Portfolio Tracker, such as the asset name, price purchased at and any screenshots they want to add related to the trade. In this way the user can build a one stop repository of their trading history. 

Using this data the Trading Portfolio Tracker can show the user their portfolio performance over multiple time periods and current holdings. The user can also make use of the in-built risk calculator to calculate the position size and number of shares to purchase for a trade. They can also link up their Binance Crypto Portfolio to the Trading Portfolio Tracker to have easy access to a live view of their cryptocurrency holdings on Binance.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [![Vue][Vue.js]][Vue-url]
* [![Laravel][Laravel.com]][Laravel-url]
* [![Bootstrap][Bootstrap.com]][Bootstrap-url]
* [![Mysql][mysql.com]][MySql-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

To get a local copy up and running follow these steps.

### Prerequisites

Download Visual Studio Code

```sh
https://code.visualstudio.com/
```

For Windows download Laragon and install to your C: drive.

```sh 
https://laragon.org/download/ 
```

For Mac download Xampp and install it to the Applications folder.

```sh 
https://www.apachefriends.org/download.html
```

Next install PHP and Composer onto your machine and set up global path variables for both so they can be used to setup the application locally:

* PHP Add Global Path - Windows

```sh
Search "Edit The System Environment Variables" on your Windows Machine

Select "Environment Variables"

Under System Variables Select "Path" and "Edit"

Add a New Path: C:\laragon\bin\php\php-7.4.7-Win32-vc15-x64 - Should be similar to this path, pointing to a PHP version within your laragon bin php folder
```

* PHP Add Global Path - Mac

```sh
For Mac OS, step by step:

First of all, open a terminal and write it: cd ~/

Create your Bash file: touch .bash_profile

You created your ".bash_profile" file, but if you would like to edit it, you should write it;

Edit your Bash profile: open -e .bash_profile

Add the line: export PATH=$PATH:/Applications/xampp/xamppfiles/bin

After that you can save from the top-left corner of screen: File → Save
```

* Composer Setup Instructions Windows

```sh
https://getcomposer.org/doc/00-intro.md#globally
```

* Composer Setup Instructions Mac

```sh
https://www.chrissy.dev/notes/install-composer-globally-on-mac-os/
```

### Installation

1. Open Visual Studio and create a folder within the laragon or xampp folder called tradingportfoliotracker. Open up a terminal in VS in the tradingportfoliotracker folder
2. Clone the repo into that folder
   ```sh
   git clone https://github.com/olliejjc/TradingPortfolioTrackerWebApp.git
   ```
2. Create a database called tradingportfoliotrackerwebapp on your local database server
3. Create a .env file within the local cloned TradingPortfolioTrackerWebApp folder. Ensure db settings match your database and database server.
4. Ensure you are running the following cmd commands from within the TradingPortfolioTrackerWebApp folder in your VS terminal
    ```sh
   cd TradingPortfolioTrackerWebApp
   ```
5. Install composer
   ```sh
   composer install
   ```
6. Generate key
    ```sh
   php artisan key:generate
   ```
7. Migrate database
    ```sh
   php artisan migrate:fresh
   ```
8. NPM Install
    ```sh
   npm install
   ```
9. Open two new terminals in VS and run one of the following commands in each terminal
   ```sh
   php artisan serve
   ```
   ```sh
   npm run watch
   ```
10. Open the link to the application in your browser usually 
   ```sh
   http://127.0.0.1:8000/
   ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE -->
## Usage

<br />

To get started with the app:

<br />

1. Register an account:

![Register Account Screenshot][register]

<br />

<br />

2. Login:

![Login Screenshot][login]

<br />

<br />

3. Update your settings and set your Percentage Risk Per Trade and Portfolio Size:

![Update User Settings Screenshot][settings]

<br />

<br />

4. Portfolio Performance and Trade History won't be displayed until at least one trade is registered

   Add a New Trade by entering Asset Name, Price Purchased At, Trade Value, Trade Size, Date Trade Opened and any relevant Screenshots(optional):

![Add New Trade Screenshot][newtrade]

<br />

<br />

5. Once you have added some trades you can view your trading history. Your trading history will consist of open and closed trades for the time period you select and any related details and screenshots:

![Trading History Open Trades Screenshot][opentrades]

<br />

![Trading History Screenshot][tradinghistory]

<br />

<br />

6.  You can select any month or all months for any year you have trades taken in. You can also view your final balance and profit/loss for the time period selected:

![Trading History P&L Screenshot][tradinghistorypl]

<br />

<br />

7.  In Trade History you can add new screenshots to a trade using the add screenshot button or you can view attached screenshots using the screenshot viewer:

![Upload Screenshots Screenshot][uploadscreenshots]

<br />

![View Screenshots Screenshot][viewscreenshots]

<br />

<br />

8.  On the Home page under dashboard you can now view your portfolio performance in the form of a chart. You can select different time periods to view portfolio performance:

![Home Screenshot][home]

<br />

![Portfolio Performance Select Screenshot][portfolioperformanceselect]

<br />

<br />

9. You can also view your open trades/current portfolio:

![Portfolio Holdings Screenshot][portfolioholdings]

<br />

<br />

10. You can use the inbuilt risk calculator to calculate position size, shares to purchase and risk based on your portfolio size and your set risk percentage per trade:

![Risk Calculator Screenshot][riskcalculator]

<br />

<br />

11. You can connect to your Binance account and view your Binance holdings by entering your Binance API Key and Secret Key in your User Settings:

![Binance Holdings 1 Screenshot][liveportfolio1]

<br />

![Binance Holdings 2 Screenshot][liveportfolio2]

<br />

<br />

12. The Trading Portfolio Tracker is also set up to work on tablet devices with a mobile view

![Ipad Screenshot][ipadview]

<br />

<br />



<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- LICENSE -->
## License

See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Oliver Campion - olliejjc16@gmail.com

Project Link: [https://github.com/olliejjc/TradingPortfolioTrackerWebApp](https://github.com/olliejjc/TradingPortfolioTrackerWebApp)

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- MARKDOWN LINKS & IMAGES -->
[contributors-shield]: https://img.shields.io/github/contributors/othneildrew/Best-README-Template.svg?style=for-the-badge
[contributors-url]: https://github.com/othneildrew/Best-README-Template/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/othneildrew/Best-README-Template.svg?style=for-the-badge
[forks-url]: https://github.com/othneildrew/Best-README-Template/network/members
[stars-shield]: https://img.shields.io/github/stars/othneildrew/Best-README-Template.svg?style=for-the-badge
[stars-url]: https://github.com/othneildrew/Best-README-Template/stargazers
[issues-shield]: https://img.shields.io/github/issues/othneildrew/Best-README-Template.svg?style=for-the-badge
[issues-url]: https://github.com/othneildrew/Best-README-Template/issues
[license-shield]: https://img.shields.io/github/license/othneildrew/Best-README-Template.svg?style=for-the-badge
[license-url]: https://github.com/othneildrew/Best-README-Template/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/othneildrew
[Vue.js]: https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D
[Vue-url]: https://vuejs.org/
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[MySQL.com]: https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white
[MySQL-url]: https://https://www.mysql.com
<!-- APP LINKS & IMAGES -->
[product-screenshot]: public/image/homepageview.png
[register]: public/image/register.png
[login]: public/image/login.png
[settings]: public/image/usersettings.png
[newtrade]: public/image/addnewtrade.png
[opentrades]: public/image/tradinghistoryopentrades.png
[tradinghistory]: public/image/tradinghistoryoverall.png
[tradinghistorypl]: public/image/tradinghistoryp&l.png
[viewscreenshots]: public/image/screenshotsviewer.png
[uploadscreenshots]: public/image/uploadscreenshots.png
[home]: public/image/homepageview.png
[portfolioperformanceselect]: public/image/portfolioperformanceselectoptions.png
[portfolioholdings]: public/image/portfolioholdings.png
[riskcalculator]: public/image/riskcalculator.png
[liveportfolio1]: public/image/liveportfoliobinance1.png
[liveportfolio2]: public/image/liveportfoliobinance2.png
[ipadview]: public/image/mobileipadview.png