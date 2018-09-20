_## Google Cloudprint Wrapper for Laravel 5

## Installation
### Laravel
This package can be installed trough **Composer**.

```sh
composer require fruitcakestudio/google-cloudprint:"^0.1"
```

If you're using Laravel <5.5 then you need to add the ServiceProvider to the providers array in config/app.php
```sh
'FruitcakeStudio\GoogleCloudPrint\ServiceProvider',
```

Optionally you can register the facade in config.app
```sh
'CloudPrint' => FruitcakeStudio\GoogleCloudPrint\Facades\CloudPrint::class,
```

You need to publish the config file for this package. This will add the file config/cloudprintr.php, where you can configure this package.

```sh
$ php artisan vendor:publish --provider="FruitcakeStudio\GoogleCloudPrint\ServiceProvider" --tag=config
```

In order for this package to work you have to define the following values in your environment file (or hardcode them in the config file):
```sh
CLOUDPRINT_PRINTER_ID=
CLOUDPRINT_AUTH_FILE=
CLOUDPRINT_APP_NAME=
```

### Google Cloudprint
#### Creating a google service account
Go to the [Service Accounts](https://console.developers.google.com/iam-admin/serviceaccounts) page. Select an existing project or create a new one. Click the **Create ServiceAccount** button at the top of the page.

Fill out the account name, ID and select a project role: `Project -> Owner`. Check the "Create new prive key" mark and pick the JSON key. **Please write down the given email address before pressing the save button!** Save this user and save the downloaded JSON file to **/storage/app** by default. Put the filename in your .env file.

#### Adding printers
 Go to the [Cloud Printers](https://www.google.com/cloudprint#printers) page, and pick either add a classic printer or add a cloud printer. Follow the given steps until you've added a printer to cloudprint.
 
 Once the printer is added go to the "Printers" tab, click your printer and press the **Share** button on the top. Enter the Email address of the service account you've wrote down earlier and press share.
 
 Finally click the printer again and this time press the details button. Press **Advanced Details** and place the Printer-ID in your .env file.
 
 
#### Accepting a printer share invite
After sharing the printer an invite has to be accepted. The easiest way is to call the `acceptPrinter` method with the printerID as parameter. This has to be done only once.
```php
CloudPrint::acceptPrinter('xxxx-xxxx-xxxx-xxxx');
```

## Usage
### Printing a stored file
You can print a stored file trough the following method
```php
CloudPrint::printStoredFile('Job Title','file.pdf')
```
This calls `Storage::get('file.pdf')` in the background, so make sure your file is in the Storage driver. Optionally you can give a printerId as the third parameter.

### Printing a file from memory
If you want to handle your own file loading, or print a file that is in memory you can use the following method
```php
CloudPrint::printFile('Job Title', $content, $mimeType)
``` 

Again you can optionally specify a printerId as the fourth parameter._