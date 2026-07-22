<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('style.css') }}">

    <style>
        .parent {
            width: 150px; height: 100px; background-color: red; border: 1px solid black;
            top: 20px; position: relative;
        }

        .enfant {
            position: absolute;
            left: 100px;
            top: 5px;
        }
    </style>
</head>
<body>
    <h2 class="box">Bon de Livraison</h2>

<p>
    Lorem, ipsum dolor sit amet consectetur adipisicing elit. Totam facilis quas, tenetur suscipit ut, veniam distinctio architecto nam labore reprehenderit asperiores blanditiis id corrupti facere officiis nisi quibusdam, delectus et?
    Aperiam fuga illo vero fugit amet atque hic voluptatibus in at sed, voluptas voluptate quos odio recusandae temporibus commodi laborum sequi assumenda tempore minima deleniti, unde quo. Dignissimos, sit aperiam.
    Velit eos dicta laborum, et ducimus provident ratione nemo corrupti ullam pariatur eum nesciunt mollitia? Dignissimos suscipit, itaque, nisi mollitia rem temporibus necessitatibus, aliquid tempora autem recusandae quisquam perferendis ea.
    Architecto fuga laborum rem incidunt molestiae vitae iusto laudantium esse? Animi ullam cumque fugit at inventore aliquid amet, laborum dolorum? Voluptate doloremque, praesentium reiciendis quos perspiciatis magnam nihil laborum quibusdam.
    Illo alias, amet perspiciatis cumque laborum distinctio libero voluptatem molestiae nemo non totam quam labore ipsam sapiente provident! Facere accusamus, in dignissimos ipsa repellat cum obcaecati vel fugit necessitatibus quidem?
    Distinctio, iusto perferendis magni quos ullam iure similique aperiam! Ipsa eum qui quisquam ad aspernatur quam culpa accusantium debitis totam deleniti officia, iste saepe unde magni fugiat. Voluptates, veniam numquam.
    Expedita quaerat incidunt necessitatibus quidem explicabo aliquid consequuntur nihil laborum et doloribus quod molestias, repellat, minus quia eveniet accusantium eligendi qui. Enim, quas dolores. Vero minus delectus vel libero soluta!
    Laudantium perspiciatis enim excepturi tempora distinctio quasi debitis dolorum? Ab porro fugit itaque velit voluptatum aspernatur, iusto unde facere molestias dolorem delectus ea ad excepturi quae, deleniti eius veritatis optio?
    Eveniet officia nostrum nulla officiis laboriosam! Vero, corporis nam facilis vitae expedita quia accusantium quidem cupiditate nesciunt ut. Placeat omnis quisquam autem natus, itaque repellat recusandae doloremque officia. Fuga, aliquid.
    Placeat provident minus voluptates eligendi cumque hic, excepturi qui in, ea iusto rem est quibusdam nihil laboriosam officia sequi neque velit dignissimos eius nulla vero. Sequi dicta ad animi temporibus.
</p>

<div class="parent">
    
    <div class="enfant" style="width:80px; height: 50px; background-color: grey; border: 1px solid black">enfant</div>
</div>

<p>
    <h1>Demo Modal</h1>
</p>
    <button id="open">Ouvrir le modal</button>
    
    <div id="modal">
        <div class="modale-content">
            <h1>Bonjour le monde !</h1>
            <button id="close">Fermer</button>
        </div>
    </div>


    <script src="{{ asset('script.js')}}"></script>
</body>
</html>