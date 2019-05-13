<html>


<!-- Main content area -->
<div id="midcolumn">
    <h1>Setup Documentation</h1>


    <h2 id="getting-started"><a class="anchor" href="#getting-started"></a>1. Getting Started</h2>
    <p>When Jemo starts, it iterates over supported CSP&#8217;s and attempts to find credentials for a user to run with.
        We call this the <code>jemo user</code>. These credentials can be found either as JVM properties or on the
        filesystem.
        When credentials are found for a CSP, Jemo validates them and if valid it checks if
        the user has the necessary permissions to run the server. If the permission validation succeeds,
        Jemo carries on with initialisation, otherwise it attempts to find credentials for the next supported CSP.
        If no user with valid credentials and permissions is found for any CSP, Jemo asks you to drive the setup process
        by printing the following log:</p>

    <pre>
        GSM is not setup yet. Please click on the following link to provide configuration: <a
            href="https://localhost:443/jemo/setup/" class="bare"
            target="_blank">https://localhost:443/jemo/setup/</a>
    </pre>

    <p>Browse to this link and select GCP.
        Jemo, offers you 3 options:</p>

    <div class="olist arabic">
        <ol class="arabic">
            <li>
                <p>Login with the jemo user credentials (useful when the jemo user has been created before).</p>
            </li>
            <li>
                <p>Ask Jemo to install the required GCP resources including the jemo user.</p>
            </li>
            <li>
                <p>Download the terraform templates to run them locally (useful when you want to modify the
                    templates).</p>
            </li>
        </ol>
    </div>


    <h3 id="login-with-the-jemo-user-credentials"><a class="anchor"
                                                     href="#login-with-the-jemo-user-credentials"></a>1.1. Login
        with the jemo user credentials</h3>

    <p>You will be asked to enter <code>project_id</code> and <code>service_account_id</code> and select
        the GCP region the jemo user is created in. The dropdown menu displays all the available GCP regions as for now.
        If the region you are looking for is missing, please type its code in the provided text input.
        Jemo attempts to locate the json key file on <code>~/.gcp/[SERVICE_ACCOUNT_ID]@[PROJECT_ID]-cred.json</code>.
        If the file is there it validates its content.
    </p>

    <p>If the credentials are valid, then Jemo checks if the following permissions are given to the <code>jemo
        user</code>:</p>
    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">
        "roles/datastore.user"
        "roles/storage.admin"
        "roles/logging.admin"</code>
    </pre>

    <p>These are the permissions needed for Jemo to run.
        In addition to these custom roles an additional <code>jemo-role</code> is required. This contains the
        <code>"iam.serviceAccounts.get"</code> and <code>"resourcemanager.projects.getIamPolicy"</code> permissions
        and is used to validate the user credentials and validate if the user is attahced to the above roles.</p>

    <p>In case of missing permissions, Jemo displays the missing permissions.
        You have to add them, e.g. by browsing to the GCP console and then come back and try again to login.

        A genuine case for this error is when you have created the <code>jemo user</code> or the <code>jemo
            policy</code>
        yourself.
        Otherwise, the <code>jemo user</code> created by Jemo will always pass this validation.
        If you created the user with Jemo and get this error,
        it means you provided the credentials of an existing GCP user different than the <code>jemo user</code>.
        Please review the credentials you entered and retry to login.
    </p>

    <p>If the permissions are valid you will be forwarded to the next setup stage
        which is to select <code>Jemo parameter sets</code>.</p>

    <h3 id="jemo-installation"><a class="anchor" href="#jemo-installation"></a>1.2. Jemo Installation</h3>
    <p>Jemo setup requires a GCP service account with the "Owner" role to run terraform with,
        we call this the <code>terraform user</code>.
        Jemo creates terraform templates to create the user and other resources.
        The terraform user is then used to run these terraform templates.</p>

    <pre class="content">
If you don&#8217;t have credentials for the terraform user, follow these steps:

 1. Create a service account with the "terraform-user" name:
    > gcloud iam service-accounts create terraform-user

 2. Attach the "Owner" role to terraform-user (replace PROJECT_ID with your project id):
    > gcloud projects add-iam-policy-binding [PROJECT_ID] --member serviceAccount:terraform-user@[PROJECT_ID].iam.gserviceaccount.com --role roles/owner

 3. Create a json key file to be used by terraform to retrieve the credentials:
    > gcloud iam service-accounts keys create terraform-user@[PROJECT_ID]-cred.json --iam-account terraform-user@[PROJECT_ID].iam.gserviceaccount.com

 4. Create a directory "~/.gcp" and copy the json key file there:
    > mkdir ~/.gcp
    > cp terraform-user@[PROJECT_ID]-cred.json ~/.gcp/
    </pre>

    <p>Jemo generates terraform templates on your filesystem under the directory where
        Jemo runs, under the <code>gcp/install/</code> directory. Then it runs terraform:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; terraform init -no-color -var-file=gcp/install/terraform.tfvars gcp/install
&gt; terraform plan -no-color -var-file=gcp/install/terraform.tfvars gcp/install
&gt; terraform apply -no-color -auto-approve -var-file=gcp/install/terraform.tfvars gcp/install</code>
    </pre>

    <pre class="content">
If the <code>terraform</code> command is not found on your path, Jemo notifies you
with <a href="https://learn.hashicorp.com/terraform/getting-started/install.html"
        target="_blank" rel="noopener">Terraform Installation Instructions</a>.
    </pre>

    <p>Besides the <code>jemo user</code>, a
        <code>jemo-role</code> is created and attached to it. Also the custom roles <code>roles/datastore.user</code>,
        <code>roles/storage.admin</code> and <code>roles/logging.admin</code> are attached to the <code>jemo user</code>.
        Finally, terraform creates the json key file <code>jemo-user@[PROJECT_ID]-cred.json</code> and Jemo copies this
        under the <code>~/.gcp</code> directory.
        Every time jemo starts, it attempts to locate this file and validate its content to figure out if the <code>jemo-user</code>
        is setup for GCP.
    </p>

    <p>The UI notifies you with all the terraform created resources and printed outputs.</p>

    <p>Behind the scenes, Jemo logs in with the <code>jemo user</code> and forwards you
        to the next setup stage which is to select <code>Jemo parameter sets</code>.</p>

    <h3 id="download-the-terraform-templates"><a class="anchor" href="#download-the-terraform-templates"></a>1.3.
        Download the Terraform Templates</h3>
    <p>Uppon clicking on the <code>DOWNLOAD</code> button, the <code>install.zip</code> fill
        will be
        downloaded. Run:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; unzip install.zip
&gt; cd install</code></pre>

    <p>You are required to provide values for the terraform user credentials.
        Create a file name with <code>terraform.tfvars</code> and set:
    </p>
    <pre>
project_id="..."
credentials_file="..."
region="..."</code>
    </pre>

    <p>Then run terraform with:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; terraform init
&gt; terraform plan
&gt; terraform apply</code></pre>

    <p>Enter <code>yes</code> when terraform asks you if you agree to create the proposed resources.
        After a while terraform will finish and print this:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">Apply complete! Resources: 14 added, 0 changed, 0 destroyed.

Outputs:

user_account_id = jemo-user</code></pre>

    <p>Besides the <code>jemo user</code>, a
        <code>jemo-role</code> is created and attached to it. Also the custom roles <code>roles/datastore.user</code>,
        <code>roles/storage.admin</code> and <code>roles/logging.admin</code> are attached to the <code>jemo user</code>.
        Finally, terraform creates the json key file <code>jemo-user@[PROJECT_ID]-cred.json</code> and you need to copy
        this
        under the <code>~/.gcp</code> directory, run:
    <pre>
> cp jemo-user\@[PROJECT_ID]-cred.json ~/.gcp
</pre>
    Every time jemo starts, it attempts to locate this file and validate its content to figure out if the <code>jemo-user</code>
    is setup for GCP.
    </p>

    <p>Then you can use the newly created <code>jemo user</code> credentials to login.
        Click the <code>LOGIN</code> button.</p>


    <h2 id="jemo-parameters"><a class="anchor" href="#jemo-parameters"></a>2. Jemo Parameters</h2>
    <p>Jemo functionality depends on global parameters that are the same for all CSP runtime implementations.
        A group of values for these parameters is called a <code>parameter set</code>.
        You can create multiple parameter sets and select to run Jemo instances
        with different parameter sets concurrently.</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title"><strong>Jemo parameters</strong></caption>
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 75%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Name</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.location</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The location name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.location.type</code>
            </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">One of [<code>CLOUD</code>, <code>ON-PROMISE</code>]
            </p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.whitelist</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">A list of module ids to allow</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.blacklist</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">A list of module ids to prevent</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.queue.polltime</code>
            </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The queue poll interval</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.log.local</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Switch between local and cloud
                logging</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.log.output</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">If local logging is enabled, this
                parameter controls the log output (e.g. STDOUT or a local file)</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>eclipse.jemo.log.level</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The log level</p></td>
        </tr>
        </tbody>
    </table>

    <p>Once a parameter set is created, the following actions take place:</p>

    <div class="olist arabic">
        <ol class="arabic">
            <li>
                <p>the local running Jemo instance updates the parameter values (it starts with default values)</p>
            </li>
            <li>
                <p>the parameter set is stored on the cloud on the blob storage of the selected CSP</p>
            </li>
            <li>
                <p>the name if the parameter set is stored on your local file system, on
                    it is stored on the file system and on the cloud on the <code>~/jemo.properties</code> file.</p>
            </li>
        </ol>
    </div>

    <p>The first ensures that you get on running Jemo in developer mode with the selected parameter values.
        The second persists the parameter set so that any Jemo (local or cloud) instance
        can pick an existing parameter set to run with.
        The third, enables you when running in development mode to stop jvm and then restart with no disruption,
        Jemo will pick the parameter set name stored on the <code>~/jemo.properties</code> file and search on the
        CSP blob storage service to retrieve it.</p>
    <p>Notice that you can browse back and create multiple parameter sets if you wish.</p>


    <h2 id="production-environment-configuration"><a class="anchor" href="#production-environment-configuration"></a>3.
        Production Environment Configuration</h2>

    <p>After one or more parameter sets are created, Jemo offers you the ability to configure a production environment.
        With this, we mean the creation of a Kubernetes cluster and worker nodes running Jemo pods.</p>

    <p>Jemo uses the <a href="https://cloud.google.com/kubernetes-engine/" target="_blank">GCP Kubernetes engine</a> to
        create a cluster.
        Based on parameters provided by the user, Jemo generates terraform templates that drive the generation of
        GCP resources. You only need to select the <code>cluster_name</code> and the <code>gcp_cluster_count</code>
        parameters, the later being the number of worker nodes.
    </p>


    <div class="paragraph">
        <p>Finally, you can optionally select how many containers you want to run with each parameter set.
            For instance, if there are 2 parameter sets and 5 Jemo containers
            (<code>gcp_cluster_count</code>),
            we may select to run 3 containers with the first parameter set, 1 container with the
            second parameter set and 1 with no parameter set (it will run with default values).</p>
    </div>

    <h3 id="create-the-cluster"><a class="anchor" href="#create-the-cluster"></a>3.1 Create the Cluster</h3>
    <p>Jemo generates the terraform templates to create the cluster under the <code>gcp/cluster</code> directory.
        You can either download them and run them on your own, or let Jemo run them.</p>

    <h4 id="create-the-cluster-jemo"><a class="anchor" href="#create-the-cluster"></a>3.1.1 Let Jemo Create the Cluster
    </h4>
    <p>Jemo needs to run the terraform command with the terraform-user.
        Therefore, it asks you to enter its credentials and if they are valid, it runs:</p>

    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; terraform init -no-color -var-file=gcp/cluster/terraform.tfvars gcp/cluster
&gt; terraform plan -no-color -var-file=gcp/cluster/terraform.tfvars gcp/cluster
&gt; terraform apply -no-color -auto-approve -var-file=gcp/cluster/terraform.tfvars gcp/cluster</code></pre>

    <p>After the cluster and worker nodes are created, Jemo has to deploy the Jemo pods to
        worker nodes. On the background Jemo uses the Kubernetes java client to deploy the pods
        (as a stetefulset) and the ingress loadbalancer service that routes requests to the running
        Jemo containers.:</p>

    <p>The whole process can take up to 15 minutes. The Jemo UI monitors the progress.</p>

    <p>In the end, the UI notifies you with the terraform crested resources and outputs,
        as well as with the URL where you can access Jemo.
        This is the external URL of the ingress load balancer.</p>

    <p>At this point, you can close your browser, the setup is complete.</p>


    <h4 id="download-the-terraform-templates-3"><a class="anchor" href="#download-the-terraform-templates-3"></a>3.1.2.
        Download the Terraform templates</h4>
    <p>Uppon clicking on the <code>DOWNLOAD</code> button, the <code>cluster.zip</code> fill will be
        downloaded. Run:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; unzip cluster.zip
&gt; cd cluster</code></pre>

    <p>Open the <code>terraform.tfvars</code> to review existing parameter values.</p>

    <p>Then run terraform with:</p>
    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; terraform init
&gt; terraform plan
&gt; terraform apply</code></pre>

    <p>Enter <code>yes</code> when terraform asks you if you agree to create the proposed resources.
        After a while terraform will finish and print this:</p>

    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">Apply complete! Resources: 1 added, 0 changed, 0 destroyed.

Outputs:

client_certificate = ...
client_key = ...
cluster_ca_certificate = ...
gcp_cluster_endpoint = ...
gcp_cluster_name = ...</code></pre>

    <p>At this point, the cluster and worker nodes are created.
        Jemo has to deploy the Jemo pods to worker nodes.
        In order to access the cluster the
        <a href="https://cloud.google.com/sdk/install" target="_blank">gcloud</a>
        command should be installed and accessible on your path.</p>


    <p>Run the following commands:</p>
    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; gcloud container clusters get-credentials jemo-cluster
&gt; kubectl create -f kubernetes/credentials.yaml
&gt; kubectl create -f kubernetes/jemo-statefulset.yaml
&gt; kubectl rollout status statefulset jemo
&gt; kubectl create -f kubernetes/jemo-svc.yaml</code></pre>

    <p>Then run the following command to find the URL where you can access Jemo:</p>
    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; kubectl get svc jemo -o=jsonpath='{.status.loadBalancer.ingress[0].ip}' -w</code></pre>
    <p>This is the external URL of the ingress load balancer that route requests to the running
        Jemo containers. </p>
    <p>At this point, you can close your browser, the setup is complete.</p>

    <h5 id="delete-the-cluster"><a class="anchor" href="#delete-the-cluster"></a>3.1.2.1 Delete the cluster</h5>
    <p>To delete the cluster, run:</p>
    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; kubectl delete secret jemo-user-cred
&gt; kubectl delete statefulset jemo
&gt; kubectl delete svc jemo
&gt; terraform destroy</code></pre>
    <p>And enter <code>yes</code> when terraform asks you if you agree to destroy the displayed resources.</p>


    <h2 id="deleting-csp-resources"><a class="anchor" href="#deleting-csp-resources"></a>4. Deleting CSP resources</h2>
    <p>Jemo offers you the option to delete CSP resources that have been created with terraform at
        a previous step. All you have to do is to select the CSP and scroll down to the <code>Delete Existing
            Resources</code>
        section. You have the options to:</p>

    <div class="olist arabic">
        <ol class="arabic">
            <li>
                <p>Delete only the cluster resources (including kubernetes resources not managed by terraform),
                    but keep the installation resources.</p>
            </li>
            <li>
                <p>Delete only the installation resources. This assumes the cluster resources are deleted or never
                    created,
                    otherwise the attempt to delete the installation resources fails.</p>
            </li>
            <li>
                <p>Delete all the existing resources.</p>
            </li>
        </ol>
    </div>

    <p>The UI monitors the deletion process and you are notified when it is finished.
        In case of an error, please read the error cause.
        This could be that some resources can not be deleted because they are still in use,
        e.g. when you have selected to delete the installation resources while the cluster resources
        are still in place. Rarely, the <code>terraform destroy</code> command that is called to delete resources
        fails due a timeout of an underlying call to the CSP REST services.
        In this case, click on the <code>RETRY</code> button.
        This will rerun the <code>terraform destroy</code> command and carry on deleting the remaining resources.
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


</html>
