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

    <p>Browse to this link and select AWS. Jemo, offers you 3 options:</p>

    <div class="olist arabic">
        <ol class="arabic">
            <li>
                <p>Login with the jemo user credentials (useful when the jemo user has been created before).</p>
            </li>
            <li>
                <p>Ask Jemo to install the required AWS resources including the jemo user.</p>
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

    <p>You will be asked to enter <code>aws_access_key_id</code> and <code>aws_access_key_id</code> and select
        the AWS region,
        the jemo user is created in. The dropdown menu displays all the available AWS regions as for now.
        If the region you are looking for is missing, please type its code in the provided text input.</p>

    <pre class="content">Every time you provide credentials, Jemo validates them and if valid,
it writes them on your filesystem on <code>~/.aws/credentials</code> file by overwritting existing content.
Keep a copy of this file, if there are credentials you want to preserve.
    </pre>

    <p>If the credentials are valid, then Jemo checks if the following permissions are given to the <code>jemo
            user</code>:</p>
    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">
        // DynamoDB actions
        "dynamodb:BatchWriteItem",
        "dynamodb:CreateTable",
        "dynamodb:DeleteTable",
        "dynamodb:ListTables",
        "dynamodb:DescribeTable",
        "dynamodb:Scan",
        "dynamodb:Query",
        "dynamodb:GetItem",

        // S3 actions
        "s3:AbortMultipartUpload",
        "s3:CreateBucket",
        "s3:DeleteObject",
        "s3:GetObject",
        "s3:ListBucketMultipartUploads",
        "s3:ListBucket",
        "s3:PutObject",

        // SQS actions
        "sqs:CreateQueue",
        "sqs:DeleteMessage",
        "sqs:DeleteQueue",
        "sqs:GetQueueUrl",
        "sqs:ListQueues",
        "sqs:SendMessage",
        "sqs:SetQueueAttributes",

        // AmazonSQSAsync
        "sqs:ReceiveMessage",

        // AWSLogsAsync
        "logs:CreateLogGroup",
        "logs:CreateLogStream",
        "logs:DescribeLogStreams",
        "logs:PutLogEvents",

        // EC2
        "ec2:DescribeTags",
        "ec2:DescribeVpcs",

        // IAM
        "iam:GetUser",
        "iam:SimulatePrincipalPolicy",
        "iam:GetRole",
        "iam:ListPolicies"</code>
    </pre>

    <p>In case of missing permissions, Jemo displays them.
        You have to add them, e.g. by browsing to the AWS console and then come back and try again to login.

        A genuine case for this error is when you have created the <code>jemo user</code> or the <code>jemo
            policy</code>
        yourself.
        Otherwise, the <code>jemo user</code> created by Jemo will always pass this validation.
        If you created the user with Jemo and get this error,
        it means you provided the credentials of an existing AWS user different than the <code>jemo user</code>.
        Please review the credentials you entered and retry to login.
    </p>

    <p>If the permissions are valid you will be forwarded to the next setup stage
        which is to select <code>Jemo parameter sets</code>.</p>

    <h3 id="jemo-installation"><a class="anchor" href="#jemo-installation"></a>1.2. Jemo Installation</h3>
    <p>You will be asked to provide credentials for a user with administrator credentials,
        we call this the <code>terraform user</code>.
        Jemo creates terraform templates to create the user and other resources.
        The terraform user is then used to run these terraform templates.</p>

    <pre class="content">
If you don&#8217;t have credentials for the terraform user, you can create
a user with "Programmatic access" and attach the "AdministratorAccess" policy.
    </pre>

    <p>Jemo generates terraform templates on your filesystem under the directory where
        Jemo runs, under the <code>aws/install/</code> directory. Then it runs terraform:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; terraform init -no-color -var-file=aws/install/terraform.tfvars aws/install
&gt; terraform plan -no-color -var-file=aws/install/terraform.tfvars aws/install
&gt; terraform apply -no-color -auto-approve -var-file=aws/install/terraform.tfvars aws/install</code>
    </pre>

    <pre class="content">
If the <code>terraform</code> command is not found on your path, Jemo notifies you
with <a href="https://learn.hashicorp.com/terraform/getting-started/install.html"
        target="_blank" rel="noopener">Terraform Installation Instructions</a>.
    </pre>

    <p>Besides the <code>jemo user</code>, a user group <code>jemo-group</code> and a policy
        <code>jemo-policy</code> are created.
        The <code>jemo-group</code> contains <code>jemo user</code> and is assigned to <code>jemo-policy</code>
        which declares all the permissions
        needed by Jemo to run.</p>

    <p>The UI notifies you with all the terraform created resources and printed outputs.
        The Jemo user credentials (<code>jemo_user_access_key_id</code> and
        <code>jemo_user_secret_access_key</code>)
        are displayed on the <code>outputs</code> section. Copy these two values on your notes for future
        reference.</p>

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
terraform_user_access_key="..."
terraform_user_secret_key="..."
region="..."</code>
    </pre>

    <p>Then run terraform with:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; terraform init
&gt; terraform plan
&gt; terraform apply</code></pre>

    <p>Enter <code>yes</code> when terraform asks you if you agree to create the proposed resources.
        After a while terraform will finish and print this:</p>

    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">Apply complete! Resources: 6 added, 0 changed, 0 destroyed.

Outputs:

jemo_user_access_key_id = ********************
jemo_user_secret_access_key = ****************************************</code></pre>

    <p>Besides the <code>jemo user</code>, a user group <code>jemo-group</code> and a policy
        <code>jemo-policy</code> are created.
        The <code>jemo-group</code> contains <code>jemo user</code> and is assigned to <code>jemo-policy</code>
        which declares all the permissions
        needed by Jemo to run.</p>
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

    <p>Jemo uses the <a href="https://aws.amazon.com/eks/" target="_blank">AWS EKS service</a> to
        create a cluster.
        Based on parameters provided by the user, Jemo generates terraform templates that drive the generation of
        AWS resources. The parameters are organized in 3 major groups.</p>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title"><strong>Cluster master parameters</strong></caption>
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 50%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Name</th>
            <th class="tableblock halign-left valign-top">Default Value</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>cluster-name</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-cluster</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The cluster name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>cluster-role-name</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-cluster-role</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The cluster role name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>cluster-security-group-name</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-cluster-security-group</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The cluster security group name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>cluster-security-group-name-tag</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-cluster</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The cluster security group name tag</p>
            </td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>workstation-external-cidr</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Your current IP</p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">Comma separated list of local
                    workstation IPs allowed to access the cluster</p></td>
        </tr>
        </tbody>
    </table>

    <br/>

    <table class="tableblock frame-all grid-all stretch">
        <caption class="title"><strong>Cluster nodes parameters</strong></caption>
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 50%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Name</th>
            <th class="tableblock halign-left valign-top">Default Value</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>worker-node-role-name</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-node-role</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The worker nodes role name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>worker-node-instance-profile-name</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-node-instance-profile</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The worker nodes instance profile
                    name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>worker-node-security-group-name</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-node-security-group</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The worker nodes security group name</p>
            </td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>launch-conf-instance-type</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>m4.large</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The AWS launch configuration instance
                    type</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>launch-conf-name-prefix</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The AWS launch configuration name
                    prefix</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>autoscaling-group-name</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The AWS autoscaling group name</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock">
                    <code>autoscaling-group-desired-capacity</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>2</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The AWS autoscaling group capacity</p>
            </td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>autoscaling-group-max-size</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>2</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The AWS autoscaling group max size</p>
            </td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>autoscaling-group-min-size</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>1</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The AWS autoscaling group min size</p>
            </td>
        </tr>
        </tbody>
    </table>

    <p>Jemo, displays a dropdown menu with all the existing user managed policies.
        If there is one with the name <code>jemo-policy</code>, it is pre selected.
        If you have selected a different name for the policy during the installation phase,
        please find the name you selected.
        Notice that uppon the selection of a policy in the dropdown menu jemo validates
        the permissions declared by the policy to check if they cover the persmissions
        needed to run Jemo. If not the user is notified by a pop-up dialog to either add the
        missing permissions or select another policy.</p>

    <p>By default, Jemo creates a new network to run the cluster
        and the related paremeters are shown in <a href="#aws_network_params">Network parameters</a>.
        Alternatively, one can select to run the cluster on an existing network.
        To do so click on the <code>OR SELECT EXISTING NETWORK</code> button,
        the network parameters dissapear and a dropdown menu with all the exisitng network names
        is displayed. Click on <code>OR SELECT NEW NETWORK</code> to return to the previous state.</p>

    <table id="aws_network_params" class="tableblock frame-all grid-all stretch">
        <caption class="title">Network parameters</caption>
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 50%;">
        </colgroup>
        <thead>
        <tr>
            <th class="tableblock halign-left valign-top">Name</th>
            <th class="tableblock halign-left valign-top">Default Value</th>
            <th class="tableblock halign-left valign-top">Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>vpc-name-tag</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-vpc</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The cluster VPC name tag</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>subnet-name-tag</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-subnet</code></p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The subnet name tag</p></td>
        </tr>
        <tr>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>internet-gateway-name-tag</code>
                </p></td>
            <td class="tableblock halign-left valign-top"><p class="tableblock"><code>jemo-internet-gateway</code></p>
            </td>
            <td class="tableblock halign-left valign-top"><p class="tableblock">The internet gateway name tag</p></td>
        </tr>
        </tbody>
    </table>
    <div class="paragraph">
        <p>Finally, you can optionally select how many containers you want to run with each parameter set.
            For instance, if there are 2 parameter sets and 5 Jemo containers
            (<code>autoscaling-group-desired-capacity</code>),
            we may select to run 3 containers with the first parameter set, 1 container with the
            second parameter set and 1 with no parameter set (will run with default values).</p>
    </div>

    <h3 id="create-the-cluster"><a class="anchor" href="#create-the-cluster"></a>3.1 Create the Cluster</h3>
    <p>Jemo generates the terraform templates to create the cluster under the <code>aws/cluster</code> directory.
        You can either download them and run them on your own, or let Jemo run them.</p>

    <h4 id="create-the-cluster-jemo"><a class="anchor" href="#create-the-cluster"></a>3.1.1 Let Jemo Create the Cluster</h4>
    <p>Jemo needs to run the terraform command with the terraform-user.
        Therefore, it asks you to enter its credentials and if they are valid, it runs:</p>

    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; terraform init -no-color -var-file=aws/cluster/terraform.tfvars aws/cluster
&gt; terraform plan -no-color -var-file=aws/cluster/terraform.tfvars aws/cluster
&gt; terraform apply -no-color -auto-approve -var-file=aws/cluster/terraform.tfvars aws/cluster</code></pre>

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

    <p>Open the <code>terraform.tfvars</code> to review existing parameter values and
        append it with values for the terraform user credentials.</p>
    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">terraform_user_access_key="..."
terraform_user_secret_key="..."</code></pre>

    <p>Then run terraform with:</p>
    <pre class="highlightjs highlight"><code class="language-asciidoc hljs" data-lang="asciidoc">&gt; terraform init
&gt; terraform plan
&gt; terraform apply</code></pre>

    <p>Enter <code>yes</code> when terraform asks you if you agree to create the proposed resources.
        After a while terraform will finish and print this:</p>

    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">Apply complete! Resources: 26 added, 0 changed, 0 destroyed.

Outputs:

aws_eks_cluster_endpoint = ...
aws_eks_worker_node_role_arn = ...
config_map_aws_auth = ...
kubeconfig = ...</code></pre>

    <p>At this point, the cluster and worker nodes are created.
        Jemo has to deploy the Jemo pods to worker nodes.
        In order to access the cluster the
        <a href="https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html" target="_blank">AWS CLI</a>
        and <a href="https://docs.aws.amazon.com/eks/latest/userguide/install-aws-iam-authenticator.html"
               target="_blank">aws-iam-authenticator</a>
        tool should be installed and accessible on your path.</p>

    <pre>
Since the cluster was created with the terraform user,
kubectl needs to use the terraform user credentials.
Make sure the <code>~/.aws/credentials</code> file has the terraform user credentials.
    </pre>

    <p>Run the following commands:</p>
    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; aws eks update-kubeconfig --name jemo-cluster
&gt; terraform output config_map_aws_auth &gt; config-map.yaml
&gt; kubectl apply -f config-map.yaml
&gt; kubectl create -f kubernetes/jemo-statefulset.yaml
&gt; kubectl rollout status statefulset jemo
&gt; kubectl create -f kubernetes/jemo-svc.yaml</code></pre>

    <p>Then run the following command to find the URL where you can access Jemo:</p>
    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; kubectl get svc jemo -o=jsonpath='{.status.loadBalancer.ingress[0].hostname}'</code></pre>
    <p>This is the external URL of the ingress load balancer that route requests to the running
        Jemo containers. You may need to wait 1-2 minutes until you can access this URL.</p>
    <p>At this point, you can close your browser, the setup is complete.</p>

    <h5 id="delete-the-cluster"><a class="anchor" href="#delete-the-cluster"></a>3.1.2.1 Delete the cluster</h5>
    <p>To delete the cluster, run:</p>
    <pre class="highlightjs highlight"><code class="language-bash hljs" data-lang="bash">&gt; kubectl delete statefulset jemo
&gt; kubectl delete svc jemo
&gt; kubectl delete -n kube-system configmap aws-auth
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

