# Contribution Guide

For french version, click [French](#comment-contribuer)

## How to Contribute

1. **Fork the repository**: Create a copy of this repository on your GitHub account by clicking the "Fork" button.

2. **Clone the repository**: Clone the forked repository to your local machine.
    ```bash
    git clone https://github.com/lauralazzaro/projet8-TodoList.git
    ```

3. **Create a branch**: Create a new branch for your changes.
    ```bash
    git checkout -b your-branch-name
    ```

4. **Make changes**: Make your modifications or additions on your branch.

5. **Commit changes**: Save your changes with a clear and descriptive commit message.
    ```bash
    git add .
    git commit -m "Description of your changes"
    ```

6. **Push changes**: Push your changes to your forked repository.
    ```bash
    git push origin your-branch-name
    ```

7. **Open a Pull Request**: Go to the original repository and open a Pull Request from your forked repository. Describe your changes in detail in the Pull Request.

## Coding Standards

- Use PSR-12 coding style.
- Add clear and helpful comments.
- Document your functions and classes using PHPDoc.
- Write unit tests for your new features or modifications.
- Use clear and intuitive name for variable and method.
- Use camel case for naming methods and variables.
- Avoid hard coding; use constants whenever possible (e.g., in `.src/Helper/Constants.php`).
- Ensure clear comments and descriptions in your commit messages.
- The official language for this project is English; use English for all comments and messages.

## Tests

Make sure all tests pass before submitting your contribution. You can run the tests with the following command:
```bash
php bin/phpunit
```
---
## Comment Contribuer

1. **Forker le dépôt** : Créez une copie de ce dépôt sur votre compte GitHub en cliquant sur le bouton "Fork".

2. **Cloner le dépôt** : Clonez le dépôt forké sur votre machine locale.
    ```bash
    git clone https://github.com/lauralazzaro/projet8-TodoList.git
    ```

3. **Créer une branche** : Créez une nouvelle branche pour vos modifications.
    ```bash
    git checkout -b nom-de-votre-branche
    ```

4. **Effectuer des modifications** : Apportez vos modifications ou ajouts sur votre branche.

5. **Committer les modifications** : Enregistrez vos modifications avec un message de commit clair et descriptif.
    ```bash
    git add .
    git commit -m "Description de vos modifications"
    ```

6. **Pousser les modifications** : Poussez vos modifications sur votre dépôt forké.
    ```bash
    git push origin nom-de-votre-branche
    ```

7. **Ouvrir une Pull Request** : Allez sur le dépôt original et ouvrez une Pull Request depuis votre dépôt forké. Décrivez vos modifications en détail dans la Pull Request.

## Normes de Codage

- Utilisez le style de codage PSR-12.
- Ajoutez des commentaires clairs et utiles.
- Documentez vos fonctions et classes en utilisant PHPDoc.
- Écrivez des tests unitaires pour vos nouvelles fonctionnalités ou modifications.
- Utilisez des noms de variables et de méthodes clairs et intuitifs.
- Utilisez la notation camel case pour nommer les méthodes et variables.
- Évitez le codage dur ; utilisez des constantes lorsque possible (par exemple, dans `.src/Helper/Constants.php`).
- Assurez-vous d'avoir des commentaires et des descriptions claires dans vos messages de commit.
- La langue officielle pour ce projet est l'anglais ; utilisez l'anglais pour tous les commentaires et messages.

## Tests

Assurez-vous que tous les tests passent avant de soumettre votre contribution. Vous pouvez exécuter les tests avec la commande suivante :
```bash
php bin/phpunit
```