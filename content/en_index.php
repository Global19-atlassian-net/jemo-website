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
  <h1><?php print $pageTitle;?></h1>
  <p>
  Eclipse Jemo aims to deliver a true multi-cloud FaaS implementation for JVM based languages. Jemo which is built to take advantage of Kubernetes provides the same event driven development pattern that you will find in many function as a service offerings with the insulation from the specific provider. In addition to an event driven FaaS development paradigm Jemo aims to provide full compatibility with the Jakarta EE and Microprofile platforms using the runtime implementation to ensure that regardless of the platform used all applications are completely cloud native.
  In building Jemo we choose to focus on embracing the technology and pace of change provided by CSP's such as Amazon (AWS), Microsoft (Azure), and Google (GCP). The major CSP's provide many of the core application software technologies that are used to build modern applications (examples are things like Pub/Sub queue systems, Streaming, BigData, HTTP/S, Batch processing, etc). Jemo aims to harness PaaS services as they become available on underlying CSP's and allow these services to be used transparently by developers to build applications while avoiding all lock-in to the underlying provider.
  Jemo furthermore provides a homogeneous application runtime environment which spans multiple networks and device topologies thus aligning with the vision for the JVM to write once and run anywhere. Jemo will take this concept to the age of Cloud. The application server will translate code deployed to it and adapt it such as to take advantage of the best technologies on the CSP of choice and then allow users to move seamlessly to an entirely different CSP without having to adapt, compile or re-write any code within the application.
  Jemo also aims to build a virtualized network layer using peer to peer networking technology to allow computation to take place across a variety of different network topologies and devices. The application server will seamlessly scale out without boundaries and be able to take advantage of computational capacity anywhere the base runtime is present. The server will also take advantage of the 0 node principle on peer to peer technology such that the network will be active even if no nodes are running and the moment in which a single node is present it will re-commence computation.
  Jemo will also provide a UI framework which can be used by developers to build modern single page web applications without the need to know Javascript or have experience in front-end development.
  </p>
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
