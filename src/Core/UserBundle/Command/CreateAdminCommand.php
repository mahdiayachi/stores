<?php

namespace Core\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Core\UserBundle\Entity\User;

use Core\UserBundle\Event\CommandRegistrationSuccess;

class CreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
       $this
        // the name of the command (the part after "bin/console")
        ->setName('stores:admin:create')
        // the short description shown while running "php bin/console list"
        ->setDescription('Creates a new admin.')
        ->setDefinition(array(
            new InputArgument('firstName', InputArgument::REQUIRED, 'The first name of the user.'),
            new InputArgument('lastName', InputArgument::REQUIRED, 'The last name of the user.'),
            new InputArgument('email', InputArgument::REQUIRED, 'The email of the user.'),
            new InputArgument('username', InputArgument::REQUIRED, 'The username of the user.'),
            new InputArgument('password', InputArgument::REQUIRED, 'The password of the user.'),
        ))
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to create a new admin and affect to it the ADMIN role...');
       
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$em = $this->getContainer()->get('doctrine')->getManager();
    	$user = new User('ROLE_SUPER_ADMIN');
    	
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setEnabled(true);

        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $event = new CommandRegistrationSuccess($user);
        $dispatcher->dispatch(CommandRegistrationSuccess::NAME, $event);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = array();

        if (!$input->getArgument('firstName')) {
            $question = new Question('Please choose a firstName:');
            $question->setValidator(function ($firstName) {
                if (empty($firstName)) {
                    throw new \Exception('firstName can not be empty');
                }

                return $firstName;
            });
            $questions['firstName'] = $question;
        }

        if (!$input->getArgument('lastName')) {
            $question = new Question('Please choose a lastName:');
            $question->setValidator(function ($lastName) {
                if (empty($lastName)) {
                    throw new \Exception('lastName can not be empty');
                }

                return $lastName;
            });
            $questions['lastName'] = $question;
        }

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }else{
                    $user = $this->getContainer()->get('fos_user.user_manager')->findUserByEmail($email);
                    if ($user) 
                        throw new \Exception('Mail address already exists, please choose another one');
                }
                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Username can not be empty');
                }else{
                    $user = $this->getContainer()->get('fos_user.user_manager')->findUserByUsername($username);
                    if ($user) 
                        throw new \Exception('Username already exists, please choose another one');
                }
                return $username;
            });
            $questions['username'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}