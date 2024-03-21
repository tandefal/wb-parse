<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Product;
use App\selenium\Driver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Console\Command;

class ProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:products-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle(): void
    {
        foreach (["футболка", "джинсы", "платье"] as $query) {
            $this->parseProducts($query);
        }
    }

    public function parseProducts($query): void
    {
        $driver = Driver::create('http://localhost:4444/', DesiredCapabilities::chrome());
        $driver->get("https://www.wildberries.ru/catalog/0/search.aspx?search=" . urlencode($query));

        $driver->wait(10)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('.product-card'))
        );

        for ($i = 0; $i < 3; $i++) {
            $driver->executeScript("window.scrollTo(0, document.body.scrollHeight)");
            sleep(1);
        }

        $products = $driver->findElements(WebDriverBy::cssSelector('.product-card'));
        foreach ($products as $product) {
            $brand = Brand::firstOrCreate([
                'name' => trim($product->findElement(WebDriverBy::cssSelector('.product-card__brand'))->getText())
            ]);
            Product::firstOrCreate([
                'name' => trim($product->findElement(WebDriverBy::cssSelector('.product-card__name'))->getText(), '/'),
                'price' => preg_replace("/[^0-9\.]/", "", $product->findElement(WebDriverBy::cssSelector('.price__lower-price'))->getText()),
                'brand_id' => $brand->id,
                'image' => $product->findElement(WebDriverBy::cssSelector('.product-card__img-wrap img'))->getAttribute('src')
            ]);
        }

        $driver->quit();
    }
}
