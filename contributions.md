# Contribution Guide

## How to Contribute

- **Fork the repository**:   
Create a copy of this repository on your GitHub account by clicking the "Fork" button.  

- **Clone the repository**:   
Clone the forked repository to your local machine.
    
  ```bash
  git clone https://github.com/lauralazzaro/projet8-TodoList.git
  ```

- **Create a branch**: Create a new branch for your changes.
    
  ```bash
  git checkout -b your-branch-name
  ```

- **Make changes**:  
Make your modifications or additions on your branch.


- **Commit changes**:  
Save your changes with a clear and descriptive commit message.
    
  ```bash
  git add .
  git commit -m "Description of your changes"  
  ```
  
- **Push changes**: Push your changes to your forked repository.
  
  ```bash
  git push origin your-branch-name
  ```

- **Open a Pull Request**:  
In the original repository and open a Pull Request from your forked repository.  
Describe your changes in detail in the Pull Request.

## Coding Standards

- Use PSR-12 coding style.
- Add clear and helpful comments.
- Document your functions and classes using PHPDoc.
- Write unit tests for your new features or modifications.
- Use clear and intuitive name for variable and method.
- Use camel case for naming methods and variables.
- Ensure clear comments and descriptions in your commit messages.


### Maintaining Coherent Route Naming

To keep route names consistent, use a clear and structured naming strategy:  
- Routes should reflect their associated controller and action (e.g. task_list for listing tasks and task_create for creating a new task).
- Use action verbs like list, create, edit, and delete to describe what the route does. 
- Group related routes with common prefixes, such as task_ for task routes and user_ for user routes.

## Testing

Make sure all tests pass before submitting your contribution. 

---

## Comment Contribuer

- **Forker le dépôt**  
Créez une copie de ce dépôt sur votre compte GitHub en cliquant sur le bouton "Fork".

- **Cloner le dépôt**

  ```bash
  git clone https://github.com/lauralazzaro/projet8-TodoList.git
  ```

- **Créer une branche**  
Créez une nouvelle branche pour vos modifications.
    
  ```bash
  git checkout -b nom-de-votre-branche
  ```

- **Effectuer des modifications**  
Apportez vos modifications ou ajouts sur votre branche.


- **Committer les modifications**  
Enregistrez vos modifications avec un message de commit clair et descriptif.
    
  ```bash
  git add .
  git commit -m "Description de vos modifications"
  ```

- **Pousser les modifications**  
Poussez vos modifications sur votre dépôt forké.
    
  ```bash
  git push origin nom-de-votre-branche
  ```

- **Ouvrir une Pull Request**  
Allez sur le dépôt original et ouvrez une Pull Request depuis votre dépôt forké.  
Décrivez vos modifications en détail dans la Pull Request.

## Normes de Codage

- Utilisez le style de codage PSR-12.
- Ajoutez des commentaires clairs et utiles.
- Documentez vos fonctions et classes en utilisant PHPDoc.
- Écrivez des tests unitaires pour vos nouvelles fonctionnalités ou modifications.
- Utilisez des noms clairs et intuitifs pour les variables et les méthodes.
- Utilisez le camel case pour nommer les méthodes et les variables.
- Assurez des commentaires et des descriptions clairs dans vos messages de commit.

### Maintien de la Cohérence des Noms de Routes

Pour garder les noms de routes cohérents, utilisez une stratégie de nommage claire et structurée :
- Les routes doivent refléter leur contrôleur et action associés (par exemple, task_list pour lister les tâches et task_create pour créer une nouvelle tâche).
- Utilisez des verbes d'action comme list, create, edit, et delete pour décrire ce que fait la route.
- Regroupez les routes liées avec des préfixes communs, tels que task_ pour les routes de tâche et user_ pour les routes d'utilisateur.
## Tests

Assurez-vous que tous les tests passent avant de soumettre votre contribution.
