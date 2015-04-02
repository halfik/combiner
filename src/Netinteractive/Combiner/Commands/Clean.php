<?php
namespace Netinteractive\Combiner\Commands;

use \Illuminate\Console\Command;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputArgument;
use \Netinteractive\Forms\Generator;

class Clean extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'combiner:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean combiner generated files';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('mask', InputArgument::REQUIRED, 'mask of file'),
            array('confirm', InputArgument::OPTIONAL, 'confirm'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(

        );
    }

    public function fire(){
        $mask=$this->argument('mask');
        $confirm=$this->argument('confirm');
        $files=glob(public_path($mask));

        #Jak są pliki
        if(count($files)){
            #Jak nie jest przekazany parametr potwierdzenia
            if(!$confirm){
                #Pokaż pliki ktore beda usuniete
                foreach($files as $file){
                    $this->info($file);
                }
                if($this->ask('Delete files?(y/n)')=='y'){
                    #Usun pliki
                    foreach($files as $file){
                        unlink($file);
                    }
                }
            }
            else{
                #Usun pliki
                foreach($files as $file){
                    unlink($file);
                }
            }
        }

    }

}