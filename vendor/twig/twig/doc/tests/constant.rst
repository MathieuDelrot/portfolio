``constant``
============

``constant`` checks if a variable has the exact same value as a constant. You
can use either global constants or class constants:

.. code-block:: twig

    {% if project.status is constant('Project::PUBLISHED') %}
        the status attribute is exactly the same as Project::PUBLISHED
    {% endif %}

You can test constants from object instances as well:

.. code-block:: twig

    {% if project.status is constant('PUBLISHED', project) %}
        the status attribute is exactly the same as Project::PUBLISHED
    {% endif %}
