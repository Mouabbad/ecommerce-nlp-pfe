<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('stock')
            ->add('categorie') // تأكد عندك علاقة مع Categorie فEntity
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit (jpg/png)',
                'mapped' => false,          // ما مربوطةش مباشرة مع الحقل في الـ Entity
                'required' => false,        // رفع الصورة مش ضروري عند التعديل
                'constraints' => [
                    new File([
                        'maxSize' => '9M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (jpeg ou png).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
