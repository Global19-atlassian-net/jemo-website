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
    <h1><?php print $pageTitle; ?></h1>

    <p>Eclipse Jemo aims to deliver a true multi-cloud FaaS implementation for JVM based languages.
        Jemo which is built to take advantage of <a href="https://kubernetes.io/">Kubernetes</a> provides the same event
        driven development pattern
        that you will find in many function as a service offerings with the insulation from the specific provider.</p>

    <p>In addition to an event driven FaaS development paradigm Jemo aims to provide full compatibility
        with the <a href="https://jakarta.ee/">Jakarta EE</a> and <a href="https://microprofile.io/">Microprofile
            platforms</a>
        using the runtime implementation to ensure that
        regardless of the platform used all applications are completely cloud native.
        In building Jemo we choose to focus on embracing the technology and pace of change provided by CSP&#8217;s
        such as <a href="https://aws.amazon.com/">Amazon (AWS)</a>, <a href="https://azure.microsoft.com/en-gb/">Microsoft
            (Azure)</a>,
        and <a href="https://cloud.google.com/">Google (GCP)</a>.</p>

    <p>The major CSP&#8217;s provide many of the core application software technologies that are used to build
        modern applications
        (examples are things like Pub/Sub queue systems, Streaming, BigData, HTTP/S, Batch processing, etc).
        Jemo aims to harness PaaS services as they become available on underlying CSP&#8217;s and allow these
        services
        to be used transparently by developers to build applications while avoiding all lock-in to the underlying
        provider.</p>

    <p>Jemo furthermore provides a homogeneous application runtime environment which spans multiple networks
        and device topologies thus aligning with the vision for the JVM to write once and run anywhere.
        Jemo will take this concept to the age of Cloud.
        The application server will translate code deployed to it and adapt it such as to take advantage
        of the best technologies on the CSP of choice and then allow users to move seamlessly
        to an entirely different CSP without having to adapt, compile or re-write any code within the
        application.</p>

    <p>Jemo also aims to build a virtualized network layer using peer to peer networking technology
        to allow computation to take place across a variety of different network topologies and devices.
        The application server will seamlessly scale out without boundaries and be able to take advantage
        of computational capacity anywhere the base runtime is present.
        The server will also take advantage of the 0 node principle on peer to peer technology
        such that the network will be active even if no nodes are running and the moment in
        which a single node is present it will re-commence computation.</p>

    <p>Jemo will also provide a UI framework which can be used by developers to build modern single page web
        applications
        without the need to know Javascript or have experience in front-end development.</p>



    <h2 id="technical-requirements"><a class="anchor" href="#technical-requirements"></a>1. Technical requirements</h2>

    <p>To run Jemo you need to have
        <a href="https://www.oracle.com/technetwork/java/javase/downloads/jre8-downloads-2133155.html" target="_blank"
           rel="noopener">Java 8 jre</a>
        (or higher) installed and accessible on your path.</p>

    <p>Jemo users <a href="https://learn.hashicorp.com/terraform/" target="_blank" rel="noopener">terraform</a> to
        create the CSP resources.
        You need to <a href="https://learn.hashicorp.com/terraform/getting-started/install.html" target="_blank"
                       rel="noopener">install terraform</a>
        and make sure it is on your path.</p>

    <p>Jemo uses kubernetes to create clusters by exploiting the dedicated kubernetes services offered by CSP&#8217;s.
        The <code>kubectl</code> command is used to create kubernetes pods.
        <a href="https://kubernetes.io/docs/tasks/tools/install-kubectl">Install kubectl</a> and make sure it is on your
        path.</p>




    <h2 id="build-jemo"><a class="anchor" href="#build-jemo"></a>2. Build Jemo</h2>

    <p>To build Jemo locally, you wil need to clone the jemo repository</p>

        <div class="listingblock">
            <div class="content">
                <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; git clone ...</code></pre>
            </div>
        </div>

            <p>And then execute the <a href="https://maven.apache.org/">Maven</a> <code>deploy</code> target on the root directory.</p>

    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; cd jemo
&gt; mvn deploy</code></pre>

            <table>
                <tr>
                    <td class="icon">
                        <i class="fa icon-tip" title="Tip"></i>
                    </td>
                    <td class="content">
                        If you don&#8217;t have <code>mvn</code> installed, please <a href="https://maven.apache.org/install.html">read these instructions</a>.
                    </td>
                </tr>
            </table>






</div>
<!-- ./end  #midcolumn -->

<!-- Start of the right column -->
<div id="rightcolumn">
    <div class="sideitem">
        <h2>Related Links</h2>
        <ul>
            <li><a target="_self" href="/jemo/roadmap.php">Roadmap</a></li>
        </ul>
    </div>
</div>
<!-- ./end  #rightcolumn -->
