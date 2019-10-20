# CQRS AND EVENTSOURCING WITH API PLATFORM TURORIAL

Part 1: https://junghanns.it/posts/cqrs-and-eventsourcing-with-api-platform-i/
Part 2: https://junghanns.it/posts/cqrs-and-eventsourcing-with-api-platform-ii/
Part 3: https://junghanns.it/posts/cqrs-and-eventsourcing-with-api-platform-iii/
Part 4: https://junghanns.it/posts/cqrs-and-eventsourcing-with-api-platform-iv/

## API Platform

<h1 align="center"><a href="https://api-platform.com"><img src="https://api-platform.com/logo-250x250.png" alt="API Platform"></a></h1>

API Platform is a next-generation web framework designed to easily create API-first projects without compromising extensibility
and flexibility:

* Design your own data model as plain old PHP classes or [**import an existing one**](https://api-platform.com/docs/schema-generator)
  from the [Schema.org](https://schema.org/) vocabulary
* **Expose in minutes a hypermedia REST or a GraphQL API** with pagination, data validation, access control, relation embedding,
  filters and error handling...
* Benefit from Content Negotiation: [GraphQL](http://graphql.org), [JSON-LD](http://json-ld.org), [Hydra](http://hydra-cg.com),
  [HAL](http://stateless.co/hal_specification.html), [JSONAPI](https://jsonapi.org/), [YAML](http://yaml.org/), [JSON](http://www.json.org/), [XML](https://www.w3.org/XML/) and [CSV](https://www.ietf.org/rfc/rfc4180.txt) are supported out of the box
* Enjoy the **beautiful automatically generated API documentation** (Swagger/[OpenAPI](https://www.openapis.org/))
* Add [**a convenient Material Design administration interface**](https://api-platform.com/docs/admin) built with [React](https://reactjs.org/)
  without writing a line of code
* **Scaffold fully functional Progressive-Web-Apps and mobile apps** built with [React](https://api-platform.com/docs/client-generator/react), [Vue.js](https://api-platform.com/docs/client-generator/vuejs) or [React Native](https://api-platform.com/docs/client-generator/react-native) thanks to [the client
  generator](https://api-platform.com/docs/client-generator) (a Vue.js generator is also available)
* Install a development environment and deploy your project in production using **[Docker](https://api-platform.com/docs/distribution#using-the-official-distribution-recommended)** and [Kubernetes](https://api-platform.com/docs/deployment/kubernetes)
* Easily add **[JSON Web Token](https://api-platform.com/docs/core/jwt) or [OAuth](https://oauth.net/) authentication**
* Create specs and tests with a **developer friendly API testing tool** on top of [Behat](http://behat.org/)

[![Build Status](https://travis-ci.org/api-platform/core.svg?branch=master)](https://travis-ci.org/api-platform/core)
[![Build status](https://ci.appveyor.com/api/projects/status/grwuyprts3wdqx5l?svg=true)](https://ci.appveyor.com/project/dunglas/dunglasapibundle)
[![Coverage Status](https://coveralls.io/repos/github/api-platform/core/badge.svg)](https://coveralls.io/github/api-platform/core)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/92d78899-946c-4282-89a3-ac92344f9a93/mini.png)](https://insight.sensiolabs.com/projects/92d78899-946c-4282-89a3-ac92344f9a93)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/api-platform/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/api-platform/core/?branch=master)

The official project documentation is available **[on the API Platform website](https://api-platform.com)**.

API Platform embraces open web standards (OpenAPI, JSON-LD, GraphQL, Hydra, HAL, JSONAPI, JWT, OAuth, HTTP...) and the [Linked Data](https://www.w3.org/standards/semanticweb/data)
movement. Your API will automatically expose structured data in Schema.org/JSON-LD.
It means that your API Platform application is usable **out of the box** with technologies of the semantic web.

It also means that **your SEO will be improved** because **[Google leverages these formats](https://developers.google.com/search/docs/guides/intro-structured-data)**.

Last but not least, the server component of API Platform is built on top of the [Symfony](https://symfony.com) framework,
while client components leverage [React](https://reactjs.org/) (a [Vue.js](https://vuejs.org/) flavor is also available).
It means than you can:

* use **thousands of Symfony bundles and React components** with API Platform
* integrate API Platform in **any existing Symfony or React application**
* reuse **all your Symfony and React skills**, benefit of the incredible amount of documentation available
* enjoy the popular [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) (used by default, but fully optional:
  you can use the data provider you want, including but not limited to MongoDB and ElasticSearch)

Install
-------

[Read the official "Getting Started" guide](https://api-platform.com/docs/distribution).

Credits
-------

Created by [Kévin Dunglas](https://dunglas.fr). Commercial support available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
