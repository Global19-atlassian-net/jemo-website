<?php
/*******************************************************************************
 * Copyright (c) 2014 Eclipse Foundation and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Christopher Guindon (Eclipse Foundation) - Initial implementation
 *******************************************************************************/
?>

<!-- Main content area -->
<div id="midcolumn">

    <h1 id="jemo-architecture"><a class="anchor" href="#jemo-architecture"></a>1. Jemo Architecture</h1>
    <p>Jemo provides a homogeneous application runtime environment which spans multiple networks
        and device topologies.
        The application server will seamlessly scale out without boundaries and be able to take advantage
        of computational capacity anywhere the base runtime is present.</p>

    <p>To achieve this goal, Jemo relies on two basic abstractions the <code>CloudRuntime</code> which abstracts away
        the CSP specific services
        and the <code>Module</code> interface. Jemo offers the following development patterns, each one modeled by a sub
        interface of <code>Module</code>.
        A Jemo application (also called Jemo <code>plugin</code>) consists of one or more modules, i.e.
        classes each one implementing one of the sub interfaces of <code>Module</code> interface.</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title">Table 1. Jemo development patterns</caption>
        <colgroup>
            <col style="width: 50%;">
            <col style="width: 50%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Development Pattern</th>
            <th class="tableblock halign-left valign-top">Interface to implement</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Web service</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>WebServiceModule</code></p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">event processing</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>EventModule</code></p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Batch processing</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>BatchModule</code></p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Fixed processing</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>FixedModule</code></p></td>
        </tr>
        </tbody>
    </table>

    <p>When your app code is ready you have to deploy the module to a running Jemo instance.
        Jemo on this instance receives the jar file for this module, extracts metadata about it and stores both the jar
        and the metadata
        to the cloud via the selected CloudRuntime implementation.
        All the Jemo instances are notified about the new module.</p>

    <h3 id="the-webservicemodule"><a class="anchor" href="#the-webservicemodule"></a>1.1. The WebServiceModule</h3>

    <p>Every time an HTTP request is received by a Jemo instance for a module implementing the web service pattern,
        Jemo looks at the modules metadata to match the current endpoint with an existing module.
        If a match is found, Jemo downloads the module jar from the cloud and deploys it locally to serve the request.
        After the request is served and after a period of inactivity the Jemo instance unloads the application.</p>

    <div class="listingblock">
        <div class="title">web service pattern</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">String getBasePath();

void process(HttpServletRequest request, HttpServletResponse response);</code></pre>
        </div>
    </div>

    <p>The <code>getBasePath</code> method provides the web service endpoint, while the <code>process</code> method
        is the place where the business logic
        for serving HTTP requests should be implemented.</p>

    <h3 id="the-eventmodule"><a class="anchor" href="#the-eventmodule"></a>1.2. The EventModule</h3>
    <p>Event processing is achieved via Jemo messages that are broadcasted across running Jemo instances. A Jemo
        instance eventually picks up the message
        and consumes it. A Jemo message declares the plugin id (app id) that generated the message, the target class
        that needs to consume the message, along
        with all other parameters needed to carry out the computation by the consumer.</p>

    <div class="listingblock">
        <div class="title">event processing pattern</div>
        <div class="content">
            <pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">JemoMessage process(JemoMessage message) throws Throwable;</code></pre>
        </div>
    </div>
    <p>The <code>process</code> method provides code to consume the JemoMessage.</p>

    <h3 id="the-batchmodule"><a class="anchor" href="#the-batchmodule"></a>1.3. The BatchModule</h3>
    <p>Batch processing is supported by scheduling a module to run the <code>processBatch</code> method once per
        a
        fixed amount of time, a.k.a. the batch frequency.</p>
    <div class="listingblock">
        <div class="title">event processing pattern</div>
        <div class="content">
            <pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">void processBatch(String location, boolean isCloudLocation) throws Throwable;</code></pre>
        </div>
    </div>

    <p>The <code>processBatch</code> method provides the code for the batch calculation to be run by Jemo.
        Optionally the developer can override the <code>getLimits</code> method of the <code>Module</code>
        interface,
        in order to control where and when the batch process run.
        The following table displays how you can control how many module instances run and where.</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title">Table 2. Module Limit Batch Options</caption>
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 75%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Method</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>setMaxActiveBatchesPerGSM(N)</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Run N instances of this module
                    across
                    Jemo instances.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>setMaxActiveFixedPerLocation(N)</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Run N instances of this module
                    across
                    Jemo instances within a location.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>setMaxActiveFixedPerInstance(N)</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Run N instances of this module on
                    each
                    running Jemo instance.</p></td>
        </tr>
        </tbody>
    </table>

    <p>If you don&#8217;t override the <code>getLimits</code> method, the default is to have at max 1 active
        batch
        process per location, that runs every 1 minute.</p>

    <h3 id="the-fixedmodule"><a class="anchor" href="#the-fixedmodule"></a>1.4. The FixedModule</h3>

    <p>Finally, with fixed processing you can write code in a module and select how many module instances and on
        what cluster or network topology.
        Options are summarized in the following table.</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title">Table 3. Module Limit Fixed Processing Options</caption>
        <colgroup>
            <col style="width: 30%;">
            <col style="width: 70%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Method</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>setMaxActiveFixedPerGSM(N)</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Run N fixed processes of
                    this module across Jemo instances</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>setMaxActiveFixedPerLocation(N)</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Run N fixed processes of
                    this module across Jemo instances within a location.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>setMaxActiveFixedPerInstance(N)</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Run N fixed processes of
                    this module on each running Jemo instance.</p></td>
        </tr>
        </tbody>
    </table>

    <div class="listingblock">
        <div class="title">event processing pattern</div>
        <div class="content">
            <pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">void processFixed(final String location, final String instanceId) throws Throwable;</code></pre>
        </div>
    </div>

    <p>Developers used to deploy their apps with kubernetes will find the fixed processing pattern familiar.
        Notice that Kubernetes is used by the Jemo setup process to spin up a cluster of Jemo instances running
        on the selected CSP.
        This is different to traditional Kubernetes use, since in our case it is used to create a cluster of
        instances of
        the application server, rather than instances of the applications. For the latter, Jemo leverages cloud
        services as abstracted by
        the selected CloudRuntime.</p>

    <h2 id="getting-started"><a class="anchor" href="#getting-started"></a>2. Getting started</h2>

    <p>In this section we show how to develop a Jemo application by implementing all the offered development patterns.
        We start by creating a new maven project and provide the following content to the
        <code>pom.xml</code>
        file.</p>
    <div class="listingblock">
        <div class="title">pom.xml</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-xml hljs" data-lang="xml">&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd"&gt;
    &lt;modelVersion&gt;4.0.0&lt;/modelVersion&gt;
    &lt;groupId&gt;org.eclipse.jemo&lt;/groupId&gt;
    &lt;artifactId&gt;jemo-tutorial&lt;/artifactId&gt;
    &lt;version&gt;1.0&lt;/version&gt;
    &lt;packaging&gt;jar&lt;/packaging&gt;

    &lt;properties&gt;
        &lt;project.build.sourceEncoding&gt;UTF-8&lt;/project.build.sourceEncoding&gt;
        &lt;maven.compiler.source&gt;1.8&lt;/maven.compiler.source&gt;
        &lt;maven.compiler.target&gt;1.8&lt;/maven.compiler.target&gt;
    &lt;/properties&gt;

    &lt;repositories&gt;
        &lt;repository&gt;
            &lt;id&gt;jemo-local&lt;/id&gt;
            &lt;url&gt;http://localhost/jemo&lt;/url&gt;
        &lt;/repository&gt;
    &lt;/repositories&gt;

    &lt;dependencies&gt;
         &lt;dependency&gt;
            &lt;groupId&gt;org.eclipse.jemo&lt;/groupId&gt;
            &lt;artifactId&gt;module-api&lt;/artifactId&gt;
            &lt;version&gt;1.0&lt;/version&gt;
            &lt;scope&gt;provided&lt;/scope&gt;
        &lt;/dependency&gt;
    &lt;/dependencies&gt;

    &lt;build&gt;
        &lt;plugins&gt;
            &lt;plugin&gt;
                &lt;artifactId&gt;maven-assembly-plugin&lt;/artifactId&gt;
                &lt;configuration&gt;
                    &lt;descriptorRefs&gt;
                        &lt;descriptorRef&gt;jar-with-dependencies&lt;/descriptorRef&gt;
                    &lt;/descriptorRefs&gt;
                &lt;/configuration&gt;
                &lt;executions&gt;
                    &lt;execution&gt;
                        &lt;id&gt;make-assembly&lt;/id&gt;
                        &lt;phase&gt;package&lt;/phase&gt;
                        &lt;goals&gt;
                            &lt;goal&gt;single&lt;/goal&gt;
                        &lt;/goals&gt;
                    &lt;/execution&gt;
                &lt;/executions&gt;
            &lt;/plugin&gt;
            &lt;plugin&gt;
                &lt;artifactId&gt;maven-deploy-plugin&lt;/artifactId&gt;
                &lt;configuration&gt;
                    &lt;skip&gt;true&lt;/skip&gt;
                &lt;/configuration&gt;
            &lt;/plugin&gt;
            &lt;plugin&gt;
                &lt;groupId&gt;org.apache.maven.plugins&lt;/groupId&gt;
                &lt;artifactId&gt;eclipse-jemo-maven-plugin&lt;/artifactId&gt;
                &lt;version&gt;1.0&lt;/version&gt;
                &lt;executions&gt;
                    &lt;execution&gt;
                        &lt;phase&gt;deploy&lt;/phase&gt;
                        &lt;goals&gt;
                            &lt;goal&gt;deploy&lt;/goal&gt;
                        &lt;/goals&gt;
                        &lt;configuration&gt;
                            &lt;outputJar&gt;${project.build.finalName}-jar-with-dependencies&lt;/outputJar&gt;
                            &lt;id&gt;1&lt;/id&gt;
                            &lt;username&gt;[JEMO_USER]@jemo.eclipse.org&lt;/username&gt;
                            &lt;password&gt;[JEMO_PASSWORD]&lt;/password&gt;
                        &lt;/configuration&gt;
                    &lt;/execution&gt;
                &lt;/executions&gt;
            &lt;/plugin&gt;
        &lt;/plugins&gt;
    &lt;/build&gt;

&lt;/project&gt;</code></pre>
        </div>
    </div>

    <p>The only maven dependency needed is that of the <code>org.eclipse.jemo:module-api:1.0</code>
        artifact.
        This dependency is not found under any maven repository. Instead, it is offered by Jemo.
        That&#8217;s why we have to declare a <code>&lt;repositoy&gt;</code> element pointing to the url of
        a
        running Jemo instance
        e.g. <a href="http://localhost/jemo" class="bare">http://localhost/jemo</a> if you run Jemo locally.
        When maven downloads the dependencies,
        it will do an HTTP request to Jemo, to download the <code>module-api</code> artifact. Jemo packs the
        needed classes along with a generated
        pom file and sends it back over HTTP.</p>

    <p>To allow the above interaction and in fact any interaction of our module with Jemo,
        we need to use the <code>eclipse-jemo-maven-plugin</code> and provide the Jemo plugin id (app id)
        and
        jemo <code>username</code> and <code>password</code>.
        The Jemo plugin id is a number that uniquely identifies a plugin (Jemo app) across all the running
        jemo
        instances.
        The <code>username</code> and <code>password</code> values are generated by Jemo the first time you
        run
        it and printed in a log statement of the form:</p>

    <div class="listingblock">
        <div class="content">
            <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">System Authorisation Configured for the First Time: Admin username: *******@jemo.eclipse.org Password: *********** store this in a safe place as it will not be repeated</code></pre>
        </div>
    </div>

    <h3 id="demo-application-description"><a class="anchor" href="#demo-application-description"></a>2.1.
        Demo
        application description</h3>

    <p>In this demo we are going to build a simple trading app with Jemo that exercises the Jemo
        development
        patterns.
        The demo app consists of traders and stocks. Each trader has an account balance, a set of stocks
        he
        owns and can set target values
        for buying or selling stocks. The source code can be found on the <a href="https://git.eclipse.org/c/jemo/jemo.git" target="_blank">jemo repository</a> under the <demo>demos/jemo-trader-app</demo> directory.</p>

    <h4 id="jemo-module-lifecycle"><a class="anchor" href="#jemo-module-lifecycle"></a>2.1.1. Jemo
        Module
        Lifecycle</h4>
    <p>Jemo modules have a lifecycle modeled as methods of the Module interface. Jemo calls each one
        of
        these methods in different stages
        of the module&#8217;s lifecycle.</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title">Table 4. Lifecycle methods</caption>
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 75%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Method</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>construct</code>
                </p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Called when the module
                    is
                    created for the first time.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>installed</code>
                </p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Called the first time
                    the
                    module is registered.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>upgraded</code>
                </p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Called whenever the
                    module
                    is replaced by a new version.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>start</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Called every time the
                    module
                    is started.</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>stop</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Called every time the
                    module
                    is stopped.</p></td>
        </tr>
        </tbody>
    </table>

    <p>For instance in our demo application we use the <code>construct</code> method to initialize
        the
        data repositories.</p>
    <div class="listingblock">
        <div class="title">construct method</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">    @Override
    public void construct(Logger logger, String name, int id, double version) {
        WebServiceModule.super.construct(logger, name, id, version);
        TRADER_REPOSITORY = new TraderRepository(getRuntime());
        STOCK_REPOSITORY = new StockRepository(getRuntime());
        STOCK_REPOSITORY.findMaxId().ifPresent(maxId -&gt; CURRENT_STOCK_ID = maxId + 1);
    }</code></pre>
        </div>
    </div>

    <p>In addition, we use the <code>installed</code> method to create NoSQL tables for storing the
        traders and stocks as collections of json objects
        and we add some initial traders and stocks.</p>

    <div class="listingblock">
        <div class="title">installed method</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">    @Override
    public void installed() {
        log.info("Installed phase. Initializing the database...");
        TRADER_REPOSITORY.init();
        STOCK_REPOSITORY.init();

        // Create 20 stocks.
        final Stock[] stocks = IntStream.range(1, CURRENT_STOCK_ID)
                .mapToObj(id -&gt; new Stock(String.valueOf(id), 100f))
                .toArray(Stock[]::new);
        STOCK_REPOSITORY.save(stocks);

        // Create 10 traders and assign 2 stock to each one of them.
        final Trader[] traders = IntStream.range(1, 11)
                .mapToObj(id -&gt; {
                    final int stockIndex = 2 * (id - 1);
                    final Trader trader = new Trader(String.valueOf(id), 1000f).acquire(stocks[stockIndex]);
                    if (stockIndex + 1 &lt; CURRENT_STOCK_ID) {
                        trader.acquire(stocks[stockIndex + 1]);
                    }
                    return trader;
                })
                .toArray(Trader[]::new);
        TRADER_REPOSITORY.save(traders);
    }</code></pre>
        </div>
    </div>

    <h4 id="implementing-the-web-service-pattern"><a class="anchor"
                                                     href="#implementing-the-web-service-pattern"></a>2.1.2.
        Implementing the Web Service Pattern</h4>
    <p>The app exposes the following REST endpoints:</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title">Table 4. Endpoinds</caption>
        <colgroup>
            <col style="width: 15%;">
            <col style="width: 10%;">
            <col style="width: 75%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Endpoint</th>
            <th class="tableblock halign-left valign-top">HTTP operation</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>market/traders</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">GET</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Returns all the existing
                    traders</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>market/traders/{id}</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">GET</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Returns the trader with
                    the
                    specified id</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>market/traders/{id}</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">PUT</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Updates (replaces) the
                    state
                    of the trader with the specified id with a new state provided as a json payload</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>market/traders/{id}</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">PATCH</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Partially updates the
                    state
                    of the trader with the specified id with part of the state provided as a json
                    payload</p>
            </td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>market/stocks</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">GET</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Returns all the existing
                    stocks</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>market/stocks/{id}</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">GET</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Returns the stock with
                    the
                    specified id</p></td>
        </tr>
        </tbody>
    </table>

    <p>Tho implemented this, we declare a class <code>Market</code></p>
    <div class="listingblock">
        <div class="title">Market.java</div>
        <div class="content">
<pre>public class Market implements WebServiceModule {

...

    @Override
    public String getBasePath() {
        return "/market";
    }

    @Override
    public void process(HttpServletRequest request, HttpServletResponse response) throws Throwable {
        final String endpoint = request.getRequestURI().substring(request.getRequestURI().indexOf(getBasePath()));
        switch (request.getMethod()) {
            case "GET":
                Matcher matcher;
                log(INFO, "endpoint: " + endpoint);
                if ((matcher = ONE_TRADER_PATTERN.matcher(endpoint)).find()) {
                    getTrader(matcher.group(1), response);
                } else if (TRADERS_PATTERN.matcher(endpoint).find()) {
                    getAllTraders(response);
                } else if ((matcher = ONE_STOCK_PATTERN.matcher(endpoint)).find()) {
                    getStock(matcher.group(1), response);
                } else if (STOCKS_PATTERN.matcher(endpoint).find()) {
                    getAllStocks(response);
                } else {
                    response.sendError(400);
                }
                break;

            case "PUT":
                if ((matcher = ONE_TRADER_PATTERN.matcher(endpoint)).find()) {
                    updateTrader(matcher.group(1), request, response);
                } else {
                    response.sendError(400);
                }
                break;

            case "PATCH":
                if ((matcher = ONE_TRADER_PATTERN.matcher(endpoint)).find()) {
                    partialUpdateTrader(matcher.group(1), request, response);
                } else {
                    response.sendError(400);
                }
                break;
            default:
                response.sendError(400);
        }
    }

    ...
}</pre>
        </div>
    </div>
    <p>By implementing the <code>getBasePath</code> method to return <code>"/market"</code>, we end
        up
        with an endpoint of the form <code>/1/v1.0/market</code>.
        Jemo, uses a prefix of the form <code>/{plugin_id}/v{version_number}</code>, where
        <code>plugin_id</code>
        is defined on <code>pom.xml</code>,
        while <code>version_number</code> is determined by the <code>Module.getVersion</code>
        implementation. The default implementation returns <code>1.0</code>,
        override this method to change it.</p>
</div>

<h4 id="implementing-the-event-processing-pattern"><a class="anchor"
                                                      href="#implementing-the-event-processing-pattern"></a>2.1.3.
    Implementing the Event Processing Pattern</h4>

<p>Whenever a trader changes his target values for buying or selling stocks,
    the demo app triggers an event by sending a JemoMessage.
    This message declares the class to consume it to be the <code>MarketMatcher</code> class,
    while
    also provide a parameter valued with the
    trader id, whose targets have been changed.</p>

<div class="listingblock">
    <div class="title">triggering events</div>
    <div class="content">
<pre>    private void partialUpdateTrader(String id, HttpServletRequest request, HttpServletResponse response) throws IOException {
        log(INFO, "partialUpdateTrader");
        final Optional&lt;Trader&gt; optionalTrader = TRADER_REPOSITORY.findById(id);
        if (optionalTrader.isPresent()) {
            final Trader trader = optionalTrader.get();
            final Trader newState = Util.fromJSONString(Trader.class, Util.toString(request.getInputStream()));
            final boolean differsInTargetValue = newState.differsInTargetValue(trader);
            trader.setStockIdToBuyTargetValue(newState.getStockIdToBuyTargetValue());
            trader.setStockIdToSellTargetValue(newState.getStockIdToSellTargetValue());
            TRADER_REPOSITORY.save(trader);
            if (differsInTargetValue) {
                triggerEvent(trader.getId());
            }
            respondWithJson(200, response, trader);
        } else {
            respondWithJson(404, response, null);
        }
    }

    private void triggerEvent(String traderId) {
        JemoMessage msg = new JemoMessage();
        msg.setModuleClass(MarketMatcher.class.getName());
        msg.setId("1");
        msg.setPluginId(1);
        msg.getAttributes().put(TRADER_ID, traderId);
        msg.send(JemoMessage.LOCATION_LOCALLY);
    }</pre>
    </div>
</div>
<div class="paragraph">
    <p>The <code>MarketMatcher</code> class implements the <code>EventModule</code> interface, overrides the
        <code>process</code>
        method to consume the message.
        It attempts to match the trader whose buy/sell target values have changed with other traders
        that may be interested in selling/buying respectively
        the same stock. In case that a match is found, it carries on with the transaction and
        updates
        the states of involved traders and stock.</p>
</div>
<div class="listingblock">
    <div class="title">MarketMatcher.java</div>
    <div class="content">
<pre>public class MarketMatcher implements EventModule {

    public static final String TRADER_ID = "TRADER_ID";

    @Override
    public JemoMessage process(JemoMessage msg) throws IOException {
        log(INFO, "Consuming message...");

        final Trader sourceTrader = TRADER_REPOSITORY.findById((String) msg.getAttributes().get(TRADER_ID)).get();

        final List&lt;Trader&gt; traders = TRADER_REPOSITORY.findAll();
        Collections.shuffle(traders);

        for (Map.Entry&lt;String, Float&gt; entry : sourceTrader.getStockIdToBuyTargetValue().entrySet()) {
            final Optional&lt;Trader&gt; targetTrader = traders.stream()
                    .filter(trader -&gt; trader != sourceTrader &amp;&amp; (trader.sellTargetValue(entry.getKey()) != null &amp;&amp; trader.sellTargetValue(entry.getKey()) &lt;= entry.getValue()))
                    .findFirst();
            if (targetTrader.isPresent()) {
                final Trader seller = targetTrader.get();
                final Float value = seller.sellTargetValue(entry.getKey());
                trade(sourceTrader, seller, entry.getKey(), value);
                break;
            }
        }

        for (Map.Entry&lt;String, Float&gt; entry : sourceTrader.getStockIdToSellTargetValue().entrySet()) {
            final Optional&lt;Trader&gt; targetTrader = traders.stream()
                    .filter(trader -&gt; trader != sourceTrader &amp;&amp; trader.buyTargetValue(entry.getKey()) != null &amp;&amp; trader.buyTargetValue(entry.getKey()) &gt;= entry.getValue())
                    .findFirst();
            if (targetTrader.isPresent()) {
                final Trader buyer = targetTrader.get();
                final Float value = buyer.buyTargetValue(entry.getKey());
                trade(buyer, sourceTrader, entry.getKey(), value);
                break;
            }
        }

        return null;
    }

    private void trade(Trader buyer, Trader seller, String stockId, Float value) {
        log(INFO, String.format("Matching buyer [%s] with seller [%s] for stock [%s] and value [%s]...", buyer.getId(), seller.getId(), stockId, value));

        final Stock stock = STOCK_REPOSITORY.findById(stockId).get();
        buyer.buy(stock, value);
        seller.sell(stock, value);
        stock.setValue(value);
        TRADER_REPOSITORY.save(buyer, seller);
        STOCK_REPOSITORY.save(stock);
        TRANSACTIONS.add(new Transaction(LocalDateTime.now(), buyer.getId(), seller.getId(), stockId, value));
    }

}</pre>
    </div>

    <h4 id="implementing-the-batch-processing-pattern"><a class="anchor"
                                                          href="#implementing-the-batch-processing-pattern"></a>2.1.4.
        Implementing the Batch Processing Pattern</h4>

    <p>The demo application models a simplistic IPO event that happens regularly and adds a new
        stock to
        the market.
        A existing trader is selected randomly as the first owner of the stock.
        To implement this behaviour, we declare the <code>MarketIPO</code> class implementing the
        <code>Module</code>
        interface and override the <code>processBatch</code>
        method. With the <code>getLimits</code> method we control when and where the batch runs.
        In this case we instruct Jemo to run a single module instance across all the running Jemo
        instances, every 30 seconds.</p>

    <div class="listingblock">
        <div class="title">MarketIPO.java</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-java hljs" data-lang="java">public class MarketIPO implements BatchModule {
    private static Random RANDOM = new Random();

    @Override
    public void processBatch(String location, boolean isCloudLocation) throws Throwable {
        final Trader trader = TRADER_REPOSITORY.findById(String.valueOf(RANDOM.nextInt(10) + 1)).get();
        final Stock stock = new Stock(String.valueOf(CURRENT_STOCK_ID++), 100f);
        log(INFO, String.format("An IPO occurred - Trader [%s] owns the stock [%s]", trader.getId(), stock.getId()));
        trader.acquire(stock);
        TRADER_REPOSITORY.save(trader);
        STOCK_REPOSITORY.save(stock);
    }

    @Override
    public ModuleLimit getLimits() {
        return ModuleLimit.newInstance()
                .setMaxActiveBatchesPerGSM(1)
                .setBatchFrequency(Frequency.of(TimeUnit.SECONDS, 30))
                .build();
    }
}</code></pre>
        </div>
    </div>
    <h4 id="implementing-the-fixed-processing-pattern"><a class="anchor"
                                                          href="#implementing-the-fixed-processing-pattern"></a>2.1.5.
        Implementing the Fixed Processing Pattern</h4>

    <p>Let&#8217;s assume we want to notify a consumer (or just log for simplicity) for all the
        transactions processed by each Jemo instance for the last 30 seconds.
        To achieve this, we implement the fixed processing pattern as shown in
        <code>MarketWatch</code>
        class.</p>
    <div class="listingblock">
        <div class="title">MarketWatch.java</div>
        <div class="content">
<pre>public class MarketWatch implements FixedModule {

    public static final List&lt;Transaction&gt; TRANSACTIONS = new ArrayList&lt;&gt;();
    public AtomicBoolean running = new AtomicBoolean(false);

    @Override
    public void start() {
        running.set(true);
    }

    @Override
    public void stop() {
        running.set(false);
    }

    @Override
    public void processFixed(String location, String instanceId) throws Throwable {
        log(INFO, String.format("Process fixed Location [%s] - instance [%s]: [%s]", location, instanceId, TRANSACTIONS));
        while (running.get()) {
            // We could send the transactions to a consumer. For demo purposes we just log them.
            TRANSACTIONS.forEach(txn -&gt; log(INFO, String.format("Txn processed in Location [%s] - instance [%s]: [%s]", location, instanceId, txn)));
            TRANSACTIONS.clear();
            TimeUnit.SECONDS.sleep(30);
        }
    }

    @Override
    public ModuleLimit getLimits() {
        return ModuleLimit.newInstance()
                .setMaxActiveFixedPerInstance(1)
                .build();
    }
}</pre>
        </div>
    </div>

    <p>With the <code>getLimits</code> method we instruct Jemo to run one process on each Jemo instance. </p>

    <p>With the <code>processFixed</code> method we define an infinite loop that logs the
        transactions
        processed the last 30 seconds by this instance and then
        sleep for 30 secs.</p>

    <p>While a fixed process is meant to run forever, there are cases where Jemo needs to restart
        the module, e.g. in case of upgrades. Make sure you provide the proper code to <code>start</code> and
        <code>stop</code> methods to initialise state or free resources.
        E.g. in our example, we make sure that the loop in the <code>processFixed</code> method terminates.
        Jemo guarantees to run the number of fixed processes you have requested at all time.
        This behaviour is very similar with the kubernetes behaviour, where you would declare how many pods you want
        to run and when one pod goes down, kubernetes would create another one to replace it.
    </p>

    <h3 id="deploy-the-demo-application-on-jemo"><a class="anchor"
                                                    href="#deploy-the-demo-application-on-jemo"></a>2.2.
        Deploy
        the Demo application on Jemo</h3>

    <p>To deploy the demo application on Jemo, make sure you have a Jemo instance running and point to
        it in the pom file. Then, run:</p>

    <div class="listingblock">
        <div class="content">
                    <pre class="highlightjs highlight"><code class="language-bash hljs"
                                                             data-lang="bash">&gt; mvn deploy</code></pre>
        </div>
    </div>
    <p>The following logs are printed by the Jemo instance specified on the <code>pom.xml</code>
        file.
    </p>

    <div class="listingblock">
        <div class="title">deployment logs</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">{JEMO-{8f20729d-dab0-4ed1-9125-d7e116bd2766}} [1_jemo-tutorial-1-1.0.jar] has not been downloaded, and will now be downloaded and unpacked.
{JEMO-{8f20729d-dab0-4ed1-9125-d7e116bd2766}} [1_jemo-tutorial-1-1.0.jar][2019-03-26T11:58:08.000] loading module classes [org.eclipse.jemo.tutorial.market.MarketIPO, org.eclipse.jemo.tutorial.market.MarketMatcher, org.eclipse.jemo.tutorial.market.Market]
{1:1.0:Market} Construct phase...
{1:1.0:Market} Installed phase. Initializing the database...
{JEMO-{8f20729d-dab0-4ed1-9125-d7e116bd2766}} [1][1.000000][Market] will process HTTP/HTTPS/WEBSOCKET requests from the base path: /1/v1.0/market</code></pre>
        </div>
    </div>

    <p>Whenever an IPO occurs, you can observe a log of the form:</p>

    <div class="listingblock">
        <div class="title">IPO logs</div>
        <div class="content">
            <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">{1:1.0:MarketIPO} An IPO occurred - Trader [10] owns the stock [21]</code></pre>
        </div>
    </div>

    <p>Whenever a trader changes his target values, you can observe a log of the following form where
        the 3rd line is only logged when a match is found:</p>

    <div class="listingblock">
        <div class="title">trader update logs</div>
        <div class="content">
<pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">{1:1.0:Market} updateTrader
{1:1.0:MarketMatcher} Consuming message...
{1:1.0:MarketMatcher} Matching buyer [8] with seller [2] for stock [3] and value [105.0]...</code></pre>
        </div>
    </div>


</div>
<!-- ./end  #midcolumn -->

<!-- Start of the right column -->
<? include("docs_menu.php"); ?>
<!-- ./end  #rightcolumn -->